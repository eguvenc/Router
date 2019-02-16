<?php

use Obullo\Router\Route;

class RouteTest extends PHPUnit_Framework_TestCase
{
    public function setup()
    {
    	$this->route = new Route(
            [
                'method' => ['GET','POST'],
                'path' => '/dummy/(?<name>\w+)',
                'handler' => 'App\Controller\DefaultController:index',
                'middleware' => [
                    'App\Middleware\Dummy',
                    'App\Middleware\Lucky',
                ],
                'host' => 'test.example.com',
                'scheme' => ['http','https'],
                '$variable' => 'test attribute'
            ]
    	);
    	$this->route->setArguments(['name' => 'test', 'id' => 5]);
    }

    public function testSetPipe()
    {
    	$this->route->setPipe('test/');
    	$this->assertEquals('/test/dummy/(?<name>\w+)', $this->route->getPattern());
    }

    public function testSetName()
    {
    	$this->route->setName('dummy');
    	$this->assertEquals('dummy', $this->route->getName());
    }

    public function testGetMethods()
    {
    	$this->assertEquals(['GET','POST'], $this->route->getMethods());
    }

    public function testGetHandler()
    {
    	$this->assertEquals('App\Controller\DefaultController:index', $this->route->getHandler());
    }

    public function testGetHost()
    {
    	$this->assertEquals('test.example.com', $this->route->getHost());
    }

    public function testSetHost()
    {
    	$this->route->setHost('test2.example.com');
    	$this->assertEquals('test2.example.com', $this->route->getHost());
    }

    public function testGetSchemes()
    {
    	$this->assertEquals(['http','https'], $this->route->getSchemes());
    }

    public function testSetSchemes()
    {
    	$this->route->setSchemes('http');
    	$this->assertEquals(['http'], $this->route->getSchemes());
    }

    public function testGetArguments()
    {
    	$this->assertEquals(['name' => 'test', 'id' => 5], $this->route->getArguments());
    }

    public function testGetArgument()
    {
    	$this->assertEquals('test', $this->route->getArgument('name'));
    }

    public function testSetArguments()
    {
    	$this->route->setArguments(['name' => 'test2', 'id' => 55]);
		$this->assertEquals(['name' => 'test2', 'id' => 55], $this->route->getArguments());
    }

    public function testRemoveSetArguments()
    {
    	$this->route->removeArgument('name');
		$this->assertEquals(['id' => 5], $this->route->getArguments());
    }

    public function testGetPattern()
    {
    	$this->assertEquals('/dummy/(?<name>\w+)', $this->route->getPattern());
    }

    public function testSetPattern()
    {
    	$this->route->setPattern('/dummy/(?<name>\w+)/(?<id>\d+)');
    	$this->assertEquals('/dummy/(?<name>\w+)/(?<id>\d+)', $this->route->getPattern());
    }

    public function testGetAttribute()
    {
        $this->assertEquals('test attribute', $this->route->getAttribute('variable'));
    }

    public function testSetAttribute()
    {
        $this->route->setAttribute('foo', 'bar');
        $this->assertEquals('bar', $this->route->getAttribute('foo'));
    }

    public function testGetStack()
    {
    	$this->assertEquals(
    		[
    			'App\Middleware\Dummy',
    			'App\Middleware\Lucky',
    		],
    		$this->route->getStack()
    	);
    }
}