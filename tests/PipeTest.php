<?php

use Obullo\Router\Pipe;
use Obullo\Router\Route;

class PipeTest extends PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->pipe = new Pipe(
            'test/',
            [
                'middleware' => [
                    'App\Middleware\Dummy',
                    'App\Middleware\Test',
                ],
                'host' => '(?<name>\w+).example.com',
                'scheme' => ['http','https'],
            ]
        );
        $this->pipe->add(
            'dummy',
            new Route(
                [
                    'method' => 'POST',
                    'path' => '/dummy/<str:name>/<int:id>',
                    'handler' => 'App\Controller\DefaultController::dummy',
                    'middleware' => ['App\Middleware\Lucky']
                ]
            )
        );
    }

    public function testAdd()
    {
        $routes = $this->pipe->getRoutes();

        $this->assertEquals('POST', $routes['test/dummy']->getMethods()[0]);
        $this->assertEquals('App\Controller\DefaultController::dummy', $routes['test/dummy']->getHandler());
        $this->assertEquals('/test/dummy/<str:name>/<int:id>/', $routes['test/dummy']->getPattern());
        $this->assertEquals('App\Middleware\Lucky', $routes['test/dummy']->getStack()[0]);
    }

    public function testGetPipe()
    {
        $this->assertEquals('/test/', $this->pipe->getPipe());
    }

    public function testSetHost()
    {
        $this->pipe->setHost('(?<name>\w+).test.com');
        $this->assertEquals($this->pipe->getHost(), '(?<name>\w+).test.com');
    }

    public function testGetHost()
    {
        $this->assertEquals($this->pipe->getHost(), '(?<name>\w+).example.com');
    }

    public function testGetSchemes()
    {
        $this->assertEquals(['http','https'], $this->pipe->getSchemes());
    }

    public function testGetStack()
    {
        $this->assertEquals(
            [
                'App\Middleware\Dummy',
                'App\Middleware\Test',
            ],
            $this->pipe->getStack()
        );
    }
}