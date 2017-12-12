<?php

class MiddlewareDispatcherTest extends PHPUnit_Framework_TestCase
{
    protected $queue;
    protected $router;
    protected $dispatcher;

    public function setUp()
    {
        $this->queue = new Obullo\Middleware\Queue;
        $this->queue->register('\App\Middleware\\');

        // Create a request
        $request  = Zend\Diactoros\ServerRequestFactory::fromGlobals();
        $request  = $request->withUri(new Zend\Diactoros\Uri("http://example.com/welcome/index/a/1"));

        $response = new Zend\Diactoros\Response;
        $this->router = new Obullo\Router\Router($request, $response, $this->queue);
        $this->router->init();

        $this->dispatcher = new Obullo\Router\MiddlewareDispatcher($request, $response, $this->router);
    }

    public function testDispatch()
    {
        $this->assertEquals(true, $this->dispatcher->dispatch("welcome.*"));
        $this->assertEquals(true, $this->dispatcher->dispatch("welcome/index/(\w)/(\d)"));
    }

    public function testExecute()
    {
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
        $this->dispatcher->execute();
        $data   = $this->queue->dequeue();
        $params = $data['argument']->getParams();

        $this->assertInstanceOf("App\Middleware\ParsedBody", $data['callable']);
        $this->assertInstanceOf("Obullo\Middleware\Argument", $data['argument']);
    }

    public function testGetArgs()
    {
        $this->dispatcher->dispatch("welcome/index/(\w)/(\d)");
        $args = $this->dispatcher->getArgs();
        $this->assertEquals($args[0], "a");
        $this->assertEquals($args[1], "1");
    }
}