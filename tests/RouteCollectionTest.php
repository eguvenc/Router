<?php

use Obullo\Middleware\StackHandler as Stack;
use Obullo\Router\RouteCollection;
use Obullo\Router\Pattern\{
    StrPattern,
    IntPattern,
    NumberPattern,
    AnyPattern,
    SlugPattern
};
class RouteCollectionTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $configArray = array(
            'patterns' => [
                new IntPattern('<id:int>'),  // \d+
                new StrPattern('<name:str>'),// \w+
                new StrPattern('<word:str>'),// \w+
                new AnyPattern('<any:str>'),
                new IntPattern('<page:int>'),
                new SlugPattern('<slug:str>'),
                new SlugPattern('<slug_:str>', '(?<$name>[\w-_]+)$'), // slug with underscore
            ]
        );
        $this->config = new Zend\Config\Config($configArray);
    }

    public function testWithStack()
    {
        $this->createRequest("http://example.com/welcome");
        $collection = (new RouteCollection($this->request, $this->config))
            ->withStack(new Stack)
            ->build();

        $collection->route('GET', 'welcome.*', 'WelcomeController->index')->middleware(new Tests\Middleware\Dummy);
        $collection->dispatch();
        $stack = $collection->getStackHandler()->getStack()->getQueue();
        $this->assertInstanceOf('Tests\Middleware\Dummy', $stack[0]);
    }

    public function testRoute()
    {
        $this->createRequest("http://example.com/welcome");
        $collection = (new RouteCollection($this->request, $this->config))
            ->build();
        $collection->route(['GET','POST'], 'welcome.*', 'WelcomeController->index');
        $r = $collection->popRoute();

        $methods = $r->getMethods();
        $pattern = $r->getPattern();
        $handler = $r->getHandler();

        $this->assertEquals('GET', $methods[0]);
        $this->assertEquals('POST', $methods[1]);
        $this->assertEquals('welcome.*', $pattern);
        $this->assertEquals('WelcomeController->index', $handler);

        $this->createRequest("http://example.com/welcome/name/34");
        $collection = (new RouteCollection($this->request, $this->config))
            ->build();
        $collection->route('GET', 'welcome/<name:str>/<id:int>', function() {
            return new Zend\Diactoros\Response\HtmlResponse('It works !');
        });
        $r = $collection->popRoute();

        $methods = $r->getMethods();
        $pattern = $r->getPattern();
        $handler = $r->getHandler();

        $this->assertEquals('GET', $methods[0]);
        $this->assertEquals('welcome/(?<name>\w+)/(?<id>\d+)', $pattern);

        $response = $handler($this->request);
        ob_start();
        echo $response->getBody();
        $result = ob_get_clean();
        $this->assertEquals("It works !", $result);        
    }

    public function testGroup()
    {
        $this->createRequest("http://example.com/group/test/a/1/blabla");
        $collection = (new RouteCollection($this->request, $this->config))
            ->build();
        $collection->group(
            'group/',
            function () use ($collection) {
                $collection->group(
                    'test/',
                    function () use ($collection) {
                        $collection->route(
                            'GET',
                            '<name:str>/<id:int>.*',
                            function () {
                                return new Zend\Diactoros\Response\HtmlResponse('It works !');
                            }
                        );
                    }
                );
            }
        );
        $handler  = $collection->dispatch()->getMatchedHandler();
        $response = $handler($this->request);
        ob_start();
        echo $response->getBody();
        $result = ob_get_clean();
        $this->assertEquals("It works !", $result);
    }

    public function testGetPath()
    {
        $this->createRequest("http://example.com/user/34");
        $collection = (new RouteCollection($this->request, $this->config))
            ->build();
        $collection->route('GET', '<name:str>/<id:int>', 'WelcomeController->index');
        $collection->dispatch();
        $handler = $collection->getMatchedHandler();

        $this->assertEquals('user/34', $collection->getPath());
        $this->assertEquals('WelcomeController->index', $handler);
    }

    public function testGetStackHandler()
    {
        $this->createRequest("http://example.com/user/34");
        $collection = (new RouteCollection($this->request, $this->config))
            ->withStack(new Stack)
            ->build();
        $collection->route('GET', '<name:str>/<id:int>', 'WelcomeController->index')->middleware(new Tests\Middleware\Dummy);
        $collection->dispatch();
        $stack = $collection->getStackHandler()->getStack()->getQueue();
        $this->assertInstanceOf('Tests\Middleware\Dummy', $stack[0]);
    }

    public function testHasRouteMatch()
    {
        $this->createRequest("http://example.com/user/34");
        $collection = (new RouteCollection($this->request, $this->config))
            ->build();
        $collection->route('GET', '<name:str>/<id:int>', 'WelcomeController->index');
        $collection->dispatch();
        $this->assertTrue($collection->hasRouteMatch());
    }

    public function testGetMatchedRoute()
    {
        $this->createRequest("http://example.com/user/34");
        $collection = (new RouteCollection($this->request, $this->config))
            ->build();
        $collection->route('GET', '<name:str>/<id:int>', 'WelcomeController->index');
        $collection->dispatch();
        $this->assertInstanceOf('Obullo\Router\RouteRuleInterface', $collection->getMatchedRoute());
    }

    public function testGroupMatches()
    {
        $this->createRequest("http://example.com/group/test/name/1");
        $collection = (new RouteCollection($this->request, $this->config))
            ->build();
        $collection->group(
            'group/',
            function () use ($collection) {
                $collection->group(
                    'test/',
                    function () use ($collection) {
                        $collection->route(
                            'GET',
                            '<name:str>/<id:int>',
                            function () {
                                return new Zend\Diactoros\Response\HtmlResponse('It works !');
                            }
                        );
                    }
                );
            }
        );
        $collection->dispatch();
        $this->assertTrue($collection->hasGroupMatch());
        $this->assertInstanceOf('Obullo\Router\RouteGroupInterface', $collection->getMatchedGroup(0));
        $this->assertEquals('group', $collection->getMatchedGroup(0)->getName());
        $this->assertEquals('test', $collection->getMatchedGroup(1)->getName());
        $this->assertEquals(2, count($collection->getMatchedGroups()));
        foreach ($collection->getMatchedGroups() as $group) {
            $this->assertInstanceOf('Obullo\Router\RouteGroupInterface', $group);
        }
    }

    public function testGetMatchedHandler()
    {
        $this->createRequest("http://example.com/welcome");
        $collection = (new RouteCollection($this->request, $this->config))
            ->build();

        $collection->route('GET', 'welcome.*', 'WelcomeController->index');
        $collection->dispatch();
        $handler = $collection->getMatchedHandler();
        $this->assertEquals('WelcomeController->index', $handler);
    }

    public function testGetRequest()
    {
        $this->createRequest("http://example.com/welcome");
        $collection = (new RouteCollection($this->request, $this->config))
            ->build();
        $this->assertInstanceOf('Psr\Http\Message\RequestInterface', $collection->getRequest());
    }

    protected function createRequest($uri)
    {
        // Create a request
        $request = Zend\Diactoros\ServerRequestFactory::fromGlobals();
        $this->request = $request->withUri(new Zend\Diactoros\Uri($uri));
    }

}