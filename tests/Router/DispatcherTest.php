<?php

class DispatcherTest extends PHPUnit_Framework_TestCase
{
    protected $router;
    protected $dispatcher;

    public function setUp()
    {
        $queue = new Obullo\Middleware\Queue;
        $queue->register('\App\Middleware\\');

        // Create a request
        $request  = Zend\Diactoros\ServerRequestFactory::fromGlobals();
        $request  = $request->withUri(new Zend\Diactoros\Uri("http://example.com/welcome/index/a/1"));

        $response = new Zend\Diactoros\Response;
        $this->router = new Obullo\Router\Router($request, $response, $queue);
        $this->router->init();

        $this->dispatcher = new Obullo\Router\Dispatcher($request, $response, $this->router);
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
        $response = $this->dispatcher->execute();

        ob_start();
        echo $response->getBody();
        $this->assertEquals(ob_get_clean(), "It works !");
    }

    public function testGetArgs()
    {
        $this->dispatcher->dispatch("welcome/index/(\w)/(\d)");
        $args = $this->dispatcher->getArgs();
        $this->assertEquals($args[0], "a");
        $this->assertEquals($args[1], "1");
    }
}