<?php

class DispatcherTest extends PHPUnit_Framework_TestCase
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

        $handler = $this->dispatcher->dispatch($this->urlMapper);
        $methods = $this->dispatcher->getMethods();

        $this->assertEquals("GET", $methods[0]);
        $this->assertEquals('WelcomeController', $handler->getClass());
        $this->assertEquals('index', $handler->getMethod());
        $this->assertEquals('a', $handler->getArgs(0));
        $this->assertEquals('1', $handler->getArgs(1));

        $this->createRequest("http://example.com/arg/test/123/bar");
        $this->router->map('POST', 'arg/test/(?<id>\d+)/(?<foo>\w+)', 'ArgumentController->index');

        $this->dispatcher->dispatch($this->urlMapper);
        $handler = $this->dispatcher->getHandler();
        $methods = $this->dispatcher->getMethods();

        $this->assertEquals("POST", $methods[0]);
        $this->assertEquals('ArgumentController', $handler->getClass());
        $this->assertEquals('index', $handler->getMethod());
        $this->assertEquals('123', $handler->getArgs('id'));
        $this->assertEquals('bar', $handler->getArgs('foo'));
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
                                $response->getBody()->write($mapper->getClass()."-".$mapper->getMethod()."-".$mapper->getArgs(0)."-".$mapper->getArgs(1));
                                return $response;
                            }
                        );
                    }
                );
            }
        );
        $handler = $this->dispatcher->dispatch($this->urlMapper);
        ob_start();
        echo $handler->getBody();
        $this->assertEquals("Welcome-index-az-110", ob_get_clean());
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
        $handler = $this->dispatcher->dispatch($this->urlMapper);
        $data   = $this->queue->dequeue();
        $params = $data['argument']->getParams();

        $this->assertInstanceOf("App\Middleware\ParsedBody", $data['callable']);
        $this->assertInstanceOf("Obullo\Middleware\Argument", $data['argument']);
    }

    protected function createRequest($uri)
    {
        // Create a request
        $request  = Zend\Diactoros\ServerRequestFactory::fromGlobals();
        $request  = $request->withUri(new Zend\Diactoros\Uri($uri));

        $response = new Zend\Diactoros\Response;
        $this->router = new Obullo\Router\Router($request, $response, $this->queue);
        $this->router->init();

        $this->dispatcher = new Obullo\Router\Dispatcher($request, $response, $this->router);
        $this->urlMapper  = new Obullo\Router\UrlMapper(
            $this->dispatcher,
            $this->router,
            [
                'separator' => '->',
                'default.method' => 'index'
            ]
        );
    }

}