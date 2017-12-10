<?php

use Obullo\Router\Router;
use Obullo\Middleware\Queue;
use Obullo\Router\Dispatcher;

class RouterTest extends PHPUnit_Framework_TestCase
{
    protected $queue;
    protected $router;
    protected $request;
    protected $response;

    public function setUp()
    {
        $this->createRequest("http://example.com/foo/bar");
    }

    public function testRestful()
    {  
        $this->router->restful(true);
        $this->assertEquals(true, $this->router->isRestful());
    }

    public function testRewrite()
    {
        $dispatcher = $this->createRequest("http://example.com/es/welcome");

        $this->router->rewrite('GET', '(?:en|de|es|tr)|/(.*)', '$1');
        $this->router->map('GET', '/welcome.*', 'Welcome/index');

        $dispatcher = new Dispatcher($this->request, $this->response, $this->router);

        $handler = $dispatcher->execute();
        $this->assertEquals("Welcome/index", $handler);
    }

    public function testInit()
    {
        $segments = $this->router->getSegments();

        $this->assertEquals($segments[0], "foo");
        $this->assertEquals($segments[1], "bar");
    }

    public function testMap()
    {
        $this->router->map('GET', '/welcome.*', 'Welcome/index');
        $r = $this->router->fetchRoutes();
        $data = current($r);
        $this->assertEquals("GET", $data['method'][0]);
        $this->assertEquals("welcome.*", $data['pattern']);
        $this->assertEquals("Welcome/index", $data['handler']);
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
        $dispatcher = new Dispatcher($this->request, $this->response, $this->router);
        $response   = $dispatcher->execute();

        ob_start();
        echo $response->getBody();
        $result = ob_get_clean();

        $this->assertEquals("It works !", $result);
    }

    public function testFetchRoutes()
    {
        $this->router->map(array('POST','GET'), 'arg/test/(?<id>\d+)/(?<foo>\w+)', 'Welcome/test');

        $r = $this->router->fetchRoutes();
        $data = current($r);
        $this->assertEquals("POST", $data['method'][0]);
        $this->assertEquals("GET", $data['method'][1]);
        $this->assertEquals("arg/test/(?<id>\d+)/(?<foo>\w+)", $data['pattern']);
        $this->assertEquals("Welcome/test", $data['handler']);
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

    public function testGetSegments()
    {
        $segments = $this->router->getSegments();

        $this->assertEquals("foo", $segments[0]);
        $this->assertEquals("bar", $segments[1]);
    }

    public function testAdd()
    {
        $this->router->add("NotAllowed", array('GET', 'POST'));
        $data   = $this->queue->dequeue();
        $params = $data['argument']->getParams();

        $this->assertInstanceOf("App\Middleware\NotAllowed", $data['callable']);
        $this->assertInstanceOf("Obullo\Middleware\Argument", $data['argument']);

        $this->assertEquals($params[0], "GET");
        $this->assertEquals($params[1], "POST");
    }

    public function createRequest($uri)
    {
        $this->queue = new Queue;
        $this->queue->register('\App\Middleware\\');

        // Create a request
        $this->request = (Zend\Diactoros\ServerRequestFactory::fromGlobals())
            ->withUri(new Zend\Diactoros\Uri($uri));

        $this->response = new Zend\Diactoros\Response;
        $this->router   = new Router($this->request, $this->response, $this->queue);
        $this->router->init();
    }
}
