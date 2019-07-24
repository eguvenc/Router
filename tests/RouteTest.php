<?php

use Obullo\Router\Route;

class RouteTest extends PHPUnit_Framework_TestCase
{
    public function setup()
    {
    	$this->route = new Route(
            ['GET','POST'],
            '/dummy/(?<name>\w+)',
            'App\Controller\DefaultController:index',
            'test.example.com',
            ['http','https'],
            [
                'App\Middleware\Dummy',
                'App\Middleware\Lucky',
            ]
    	);
    	$this->route->setArguments(['name' => 'test', 'id' => 5]);
    }

    public function testGetName()
    {
    	$this->assertEquals('/dummy/(?<name>\w+)', $this->route->getName());
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

    public function testGetPath()
    {
    	$this->assertEquals('/dummy/(?<name>\w+)/', $this->route->getPath());
    }

    public function testSetPath()
    {
    	$this->route->setPath('/dummy/(?<name>\w+)/(?<id>\d+)');
    	$this->assertEquals('/dummy/(?<name>\w+)/(?<id>\d+)', $this->route->getPath());
    }

    public function testGetMiddlewares()
    {
    	$this->assertEquals(
    		[
    			'App\Middleware\Dummy',
    			'App\Middleware\Lucky',
    		],
    		$this->route->getMiddlewares()
    	);
    }
}