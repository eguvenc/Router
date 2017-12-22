<?php

class UrlMapperTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->createRequest("http://example.com/foo/bar/first/second/third");
    }

    public function testExecute()
    {
        $this->router->map('GET', 'foo/bar/([a-z]+)/([a-z]+)/([a-z]+)', 'WelcomeController->index');

        $this->dispatcher->dispatch($this->urlMapper);
        $this->urlMapper->execute();

        $this->assertEquals("Foo", $this->urlMapper->getClass());
        $this->assertEquals("bar", $this->urlMapper->getMethod());

        $this->assertEquals("first", $this->urlMapper->getArgs(0));
        $this->assertEquals("second", $this->urlMapper->getArgs(1));
        $this->assertEquals("third", $this->urlMapper->getArgs(2));
    }

    public function testSetBundle()
    {
        $this->urlMapper->setBundle("backend");
        $this->assertEquals("Backend", $this->urlMapper->getBundle());
    }

    public function testSetClass()
    {
        $this->urlMapper->setClass("welcome");
        $this->assertEquals("Welcome", $this->urlMapper->getClass());
    }

    public function testSetMethod()
    {
        $this->urlMapper->setMethod("index");
        $this->assertEquals("index", $this->urlMapper->getMethod());
    }

    public function testUnsetBundle()
    {
        $this->urlMapper->setBundle("backend");
        $this->urlMapper->unsetBundle();
        $this->assertEquals(null, $this->urlMapper->getBundle());
    }

    public function testUnsetClass()
    {
        $this->urlMapper->setClass("welcome");
        $this->urlMapper->unsetClass();
        $this->assertEquals(null, $this->urlMapper->getClass());
    }

    public function testUnsetMethod()
    {
        $this->urlMapper->setMethod("test");
        $this->urlMapper->unsetMethod();
        $this->assertEquals(null, $this->urlMapper->getMethod());
    }

    public function testUnsetArgs()
    {
        $this->urlMapper->setArgs(
            [
                'foo',
                'bar'
            ]
        );
        $this->urlMapper->unsetArgs();
        $this->assertEquals(array(), $this->urlMapper->getArgs());
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
            $this->router,
            [
                'separator' => '->',
                'default.method' => 'index'
            ]
        );
    }


}