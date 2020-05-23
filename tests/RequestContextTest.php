<?php

use Obullo\Router\RequestContext;

class RequestContextTest extends PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->request = Laminas\Diactoros\ServerRequestFactory::fromGlobals();
        $this->context = new RequestContext;
    }

    public function testFromRequest()
    {
        $this->assertInstanceOf('Obullo\Router\RequestContext', $this->context->fromRequest($this->request));
    }

    public function testGetPath()
    {
        $this->assertEquals(null, $this->context->getPath());
    }

    public function testSetPath()
    {
        $this->context->setPath('/test/path');
        $this->assertEquals('/test/path/', $this->context->getPath());   
    }

    public function testSetMethod()
    {
        $this->context->setMethod('POST');
        $this->assertEquals('POST', $this->context->getMethod());
    }

    public function testGetMethod()
    {
        $this->assertEquals(null, $this->context->getMethod());
    }

    public function testSetHost()
    {
        $this->context->setHost('test.example.com');
        $this->assertEquals('test.example.com', $this->context->getHost());
    }

    public function testGetHost()
    {
        $this->assertEquals(null, $this->context->getHost());
    }

    public function testSetScheme()
    {
        $this->context->setScheme('http');
        $this->assertEquals('http', $this->context->getScheme());
    }

    public function testGetScheme()
    {
        $this->assertEquals(null, $this->context->getScheme());
    }
}