<?php

class UrlMapperTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->queue = new Obullo\Middleware\Queue;
        $this->queue->register('\App\Middleware\\');
    }

    public function testDispatch()
    {
        $this->createRequest("http://example.com/welcome/index/a/1");
        $this->router->map('GET', 'welcome/index/(\w)/(\d)', 'WelcomeController->index');

        $this->mapper->dispatch();
        $handler = $this->mapper->getHandler();
        $path    = explode("->", $handler);
        $methods = $this->mapper->getMethods();

        $this->assertEquals("GET", $methods[0]);
        $this->assertEquals('WelcomeController', $path[0]);
        $this->assertEquals('index', $path[1]);
        $this->assertEquals('a', $this->mapper->getArgs(0));
        $this->assertEquals('1', $this->mapper->getArgs(1));

        $this->createRequest("http://example.com/arg/test/123/bar");
        $this->router->map('POST', 'arg/test/(?<id>\d+)/(?<foo>\w+)', 'ArgumentController->index');

        $handler = $this->mapper->dispatch();
        $path    = explode("->", $handler);
        $methods = $this->mapper->getMethods();

        $this->assertEquals("POST", $methods[0]);
        $this->assertEquals('ArgumentController', $path[0]);
        $this->assertEquals('index', $path[1]);
        $this->assertEquals('123', $this->mapper->getArgs('id'));
        $this->assertEquals('bar', $this->mapper->getArgs('foo'));
    }

    public function testGroupDispatch()
    {
        $this->createRequest("http://example.com/welcome/index/az/110");
        $router = $this->router;

        $router->group(
            'welcome/',
            function () use ($router) {
                $router->group(
                    'index/',
                    function () use ($router) {
                        $router->map(
                            'GET',
                            '([a-z]+)/(\d+).*',
                            function ($request, $response, $mapper) use ($router) {
                                $path = $mapper->getPathArray();
                                $response->getBody()->write($path[0]."-".$path[1]."-".$mapper->getArgs(0)."-".$mapper->getArgs(1));
                                return $response;
                            }
                        );
                    }
                );
            }
        );
        $handler  = $this->mapper->dispatch();
        $response = $handler($this->request, $this->response, $this->mapper);
        ob_start();
        echo $response->getBody();
        $this->assertEquals("welcome-index-az-110", ob_get_clean());
    }

    public function testMiddleware()
    {
        $this->createRequest("http://example.com/welcome/index/az/110");
        $router = $this->router;
        $router->group(
            'welcome/',
            function () use ($router) {
                $router->group(
                    'index/',
                    function () use ($router) {
                        $router->map('GET','/(\w+)/(\d+).*')->add('ParsedBody');
                    }
                );
            }
        );
        $handler = $this->mapper->dispatch();
        $data    = $this->queue->dequeue();
        $params  = $data['argument']->getParams();

        $this->assertInstanceOf("App\Middleware\ParsedBody", $data['callable']);
        $this->assertInstanceOf("Obullo\Middleware\Argument", $data['argument']);
    }

    public function testUnsetArgs()
    {   
        $this->createRequest("http://example.com/welcome/index/101");
        $this->mapper->setArgs(
            [
                'foo',
                'bar'
            ]
        );
        $this->mapper->unsetArgs();
        $this->assertEquals(array(), $this->mapper->getArgs());
    }

    public function testGetPathArray()
    {
        $this->createRequest("http://example.com/welcome/index/101");

        $array = $this->mapper->getPathArray();

        $this->assertEquals($array[0], "welcome");
        $this->assertEquals($array[1], "index");
        $this->assertEquals($array[2], "101");
    }

    public function testGetPattern()
    {
        $this->createRequest("http://example.com/arg/test/18/text");
        $this->router->map(array('POST','GET'), 'arg/test/(?<id>\d+)/(?<text>\w+)', 'TestController->test');
        $this->router->popRoute();
        $this->assertEquals("#^arg/test/(?<id>\d+)/(?<text>\w+)$#", $this->mapper->getPattern());
    }

    protected function createRequest($uri)
    {
        // Create a request
        $request  = Zend\Diactoros\ServerRequestFactory::fromGlobals();
        $this->request  = $request->withUri(new Zend\Diactoros\Uri($uri));

        $this->response = new Zend\Diactoros\Response;
        $this->router = new Obullo\Router\Router($this->request, $this->response, $this->queue);
        $this->router->init();

        $this->mapper = new Obullo\Router\UrlMapper($this->router);
    }

}