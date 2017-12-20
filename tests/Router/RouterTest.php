<?php

class RouterTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->createRequest("http://example.com/foo/bar");
    }

    public function testRewrite()
    {
        $this->createRequest("http://example.com/es/welcome");

        $this->router->rewrite('GET', '(?:en|de|es|tr)|/(.*)', '$1');
        $this->router->map('GET', 'welcome.*', 'WelcomeController->index');
        
        $handler = $this->dispatcher->dispatch($this->urlMapper);

        $this->assertEquals('WelcomeController', $handler->getClass());
        $this->assertEquals('index', $handler->getMethod());
    }

    public function testInit()
    {
        $segments = $this->router->getSegments();
        $this->assertEquals($segments[0], "foo");
        $this->assertEquals($segments[1], "bar");
    }

    public function testMap()
    {
        $this->createRequest("http://example.com/welcome");

        $this->router->map('GET', 'welcome.*', 'WelcomeController->index');
        $r = $this->router->popRoute();
        $this->assertEquals("GET", $r['method'][0]);
        $this->assertEquals("welcome.*", $r['pattern']);
        $this->assertEquals("WelcomeController->index", $r['handler']);
    }

    public function testGet()
    {
        $this->createRequest("http://example.com/welcome");

        $this->router->get('welcome.*', 'WelcomeController->index');
        $r = $this->router->popRoute();
        $this->assertEquals("GET", $r['method'][0]);
        $this->assertEquals("welcome.*", $r['pattern']);
        $this->assertEquals("WelcomeController->index", $r['handler']);
    }

    public function testPost()
    {
        $this->createRequest("http://example.com/welcome");

        $this->router->post('welcome.*', 'WelcomeController->index');
        $r = $this->router->popRoute();
        $this->assertEquals("POST", $r['method'][0]);
        $this->assertEquals("welcome.*", $r['pattern']);
        $this->assertEquals("WelcomeController->index", $r['handler']);
    }

    public function testPut()
    {
        $this->createRequest("http://example.com/welcome");

        $this->router->put('welcome.*', 'WelcomeController->index');
        $r = $this->router->popRoute();
        $this->assertEquals("PUT", $r['method'][0]);
        $this->assertEquals("welcome.*", $r['pattern']);
        $this->assertEquals("WelcomeController->index", $r['handler']);
    }

    public function testPatch()
    {
        $this->createRequest("http://example.com/welcome");

        $this->router->patch('welcome.*', 'WelcomeController->index');
        $r = $this->router->popRoute();
        $this->assertEquals("PATCH", $r['method'][0]);
        $this->assertEquals("welcome.*", $r['pattern']);
        $this->assertEquals("WelcomeController->index", $r['handler']);
    }

    public function testDelete()
    {
        $this->createRequest("http://example.com/welcome");

        $this->router->delete('welcome.*', 'WelcomeController->index');
        $r = $this->router->popRoute();
        $this->assertEquals("DELETE", $r['method'][0]);
        $this->assertEquals("welcome.*", $r['pattern']);
        $this->assertEquals("WelcomeController->index", $r['handler']);
    }

    public function testOptions()
    {
        $this->createRequest("http://example.com/welcome");

        $this->router->map('OPTIONS', 'welcome.*', 'WelcomeController->index');
        $r = $this->router->popRoute();
        $this->assertEquals("OPTIONS", $r['method'][0]);
        $this->assertEquals("welcome.*", $r['pattern']);
        $this->assertEquals("WelcomeController->index", $r['handler']);
    }

    public function testGroup()
    {
        $dispatcher = $this->createRequest("http://example.com/group/test/a/1");
        $router = $this->router;
        $this->router->group(
            'group/',
            function () use ($router) {
                $router->group(
                    'test/',
                    function () use ($router) {
                        $router->map(
                            'GET',
                            '/(\w+)/(\d+).*',
                            function ($request, $response, $args = null) use ($router) {
                                $response->getBody()->write("It works !");
                                return $response;
                            }
                        );
                    }
                );
            }
        );
        $response = $this->dispatcher->dispatch($this->urlMapper);
        ob_start();
        echo $response->getBody();
        $result = ob_get_clean();
        $this->assertEquals("It works !", $result);
    }

    public function testPopRoute()
    {
        $this->createRequest("http://example.com/arg/test/18/text");
        $this->router->map(array('POST','GET'), 'arg/test/(?<id>\d+)/(?<text>\w+)', 'TestController/test');
        $r = $this->router->popRoute();
        $this->assertEquals("POST", $r['method'][0]);
        $this->assertEquals("GET", $r['method'][1]);
        $this->assertEquals("arg/test/(?<id>\d+)/(?<text>\w+)", $r['pattern']);
        $this->assertEquals("TestController/test", $r['handler']);
    }

    public function testGetPath()
    {
        $this->assertEquals("/foo/bar", $this->router->getPath());
    }

    public function testGetGroup()
    {
        $this->router->group('group/', function () {});
        $this->assertInstanceOf("Obullo\Router\Group", $this->router->getGroup());
    }

    public function testGetQueue()
    {
        $this->assertInstanceOf("Obullo\Middleware\Queue", $this->router->getQueue());
    }

    public function testHasMatch()
    {
        $this->createRequest("http://example.com/welcome");
        $this->router->map('GET', 'welcome.*', 'WelcomeController->index');
        $this->router->popRoute();
        $this->assertTrue($this->router->hasMatch());
    }

    public function testGetPattern()
    {
        $this->createRequest("http://example.com/arg/test/18/text");
        $this->router->map(array('POST','GET'), 'arg/test/(?<id>\d+)/(?<text>\w+)', 'TestController/test');
        $this->router->popRoute();
        $this->assertEquals("#^arg/test/(?<id>\d+)/(?<text>\w+)$#", $this->router->getPattern());
    }

    public function testGetSegments()
    {
        $segments = $this->router->getSegments();
        $this->assertEquals("foo", $segments[0]);
        $this->assertEquals("bar", $segments[1]);
    }

    public function testAdd()
    {
        $this->router->add("NotAllowed", array('GET', 'POST'));
        $data   = $this->queue[0];
        $params = $data['argument']->getParams();

        $this->assertInstanceOf("App\Middleware\NotAllowed", $data['callable']);
        $this->assertInstanceOf("Obullo\Middleware\Argument", $data['argument']);

        $this->assertEquals($params[0], "GET");
        $this->assertEquals($params[1], "POST");
    }

    public function createRequest($uri)
    {
        $this->queue = new Obullo\Middleware\Queue;
        $this->queue->register('\App\Middleware\\');

        // Create a request
        $request  = Zend\Diactoros\ServerRequestFactory::fromGlobals();
        $request  = $request->withUri(new Zend\Diactoros\Uri($uri));

        $response = new Zend\Diactoros\Response;
        $this->router = new Obullo\Router\Router($request, $response, $this->queue);
        $this->router->init();

        $this->dispatcher = new Obullo\Router\Dispatcher($request, $response, $this->router);
        $this->urlMapper  = new Obullo\Router\UrlMapper(
            $this->dispatcher,
            [
                'path' => $this->router->getPath(),
                'separator' => '->',
                'default.method' => 'index'
            ]
        );
    }
}
