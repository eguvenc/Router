<?php

class RouteGroupTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->routeGroup = new Obullo\Router\RouteGroup(
            'users/',
            function () {
                return true;
            }
        );
    }

    public function testGetPath()
    {
        $this->assertEquals('users/', $this->routeGroup->getPath());
    }

    public function testGetName()
    {
        $this->assertEquals('users', $this->routeGroup->getName());
    }

    public function testGetCallable()
    {
        $callable = $this->routeGroup->getCallable();
        $this->assertTrue($callable());
    }

    public function testMiddleware()
    {
        $this->routeGroup->middleware(new Tests\Middleware\Dummy)
            ->middleware(new Tests\Middleware\App);
        $stack = $this->routeGroup->getMiddlewareStack();
        $this->assertInstanceOf('Tests\Middleware\Dummy', $stack[0]);
    }

    protected function createRequest($uri)
    {
        // Create a request
        $request = Zend\Diactoros\ServerRequestFactory::fromGlobals();
        $this->request = $request->withUri(new Zend\Diactoros\Uri($uri));
    }

}