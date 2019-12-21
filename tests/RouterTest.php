<?php

use Obullo\Router\Pattern;
use Obullo\Router\Route;
use Obullo\Router\Router;
use Obullo\Router\RequestContext;
use Obullo\Router\RouteCollection;
use Obullo\Router\Types\StrType;
use Obullo\Router\Types\IntType;
use Obullo\Router\Types\BoolType;
use Obullo\Router\Types\SlugType;
use Obullo\Router\Types\AnyType;
use Obullo\Router\Types\TranslationType;

class RouterTest extends PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $request = Zend\Diactoros\ServerRequestFactory::fromGlobals();
        $this->pattern = new Pattern(
            [
                new IntType('<int:id>'),
                new StrType('<str:name>'),
                new StrType('<str:word>'),
                new AnyType('<any:any>'),
                new BoolType('<bool:status>'),
                new IntType('<int:page>'),
                new SlugType('<slug:slug>'),
                new TranslationType('<locale:locale>'),
            ]
        );
        $this->context = new RequestContext;
        $this->context->setPath('/test/dummy/test/55');
        $this->context->setMethod('GET');
        $this->context->setHost('test.example.com');
        $this->context->setScheme('http');
    }

    public function testPopRoute()
    {
        $collection = new RouteCollection($this->pattern);
        $collection->setContext($this->context);
        $collection->add(
            new Route(
                'GET',
                '/test/dummy/<str:name>/<int:id>',
                'App\Controller\DefaultController::dummy',
                'test.example.com',
                'http'
            )
        );
        $router = new Router($collection);
        $route = $router->popRoute();
        $this->assertEquals('/test/dummy/<str:name>/<int:id>', $route->getName());
        $this->assertEquals('/test/dummy/(?<name>\w+)/(?<id>\d+)/', $route->getPath());
        $this->assertEquals('App\Controller\DefaultController::dummy', $route->getHandler());
    }

    public function testMatch()
    {
        $collection = new RouteCollection($this->pattern);
        $collection->setContext($this->context);
        $collection->add(
            new Route(
                'GET',
                '/dummy/<str:name>/<int:id>',
                'App\Controller\DefaultController::dummy',
                '(?<name>\w+).example.com',
                'https'
            )
        );
        $router = new Router($collection);
        $route = $router->match('/dummy/test/55', 'admin.example.com', 'https');
        $args = $route->getArguments();
        $this->assertEquals('test', $args['name']);
        $this->assertEquals('55', $args['id']);
        $this->assertEquals('/dummy/(?<name>\w+)/(?<id>\d+)/', $route->getPath());
        $this->assertEquals('admin', $router->getHostMatches()['name']);
    }

    public function testMatchRequest()
    {
        $collection = new RouteCollection($this->pattern);
        $collection->setContext($this->context);
        $collection->add(
            new Route(
                'GET',
                '/test/dummy/<str:name>/<int:id>',
                'App\Controller\DefaultController::dummy',
                '(?<name>\w+).example.com',
                'http'
            )
        );
        $router = new Router($collection);
        $route = $router->matchRequest();
        $args = $route->getArguments();
        $this->assertEquals('test', $args['name']);
        $this->assertEquals('55', $args['id']);
        $this->assertEquals('/test/dummy/(?<name>\w+)/(?<id>\d+)/', $route->getPath());
        $this->assertEquals('test', $router->getHostMatches()['name']);
    }

    public function testGetMiddleware()
    {
        $collection = new RouteCollection($this->pattern);
        $collection->setContext($this->context);
        $collection->add(
            new Route(
                'GET',
                '/test/dummy/<str:name>/<int:id>',
                'App\Controller\DefaultController::dummy',
                '(?<name>\w+).example.com',
                'http',
                ['App\Middleware\Dummy','App\Middleware\Test']
            )
        );
        $router = new Router($collection);
        $router->matchRequest();
        $this->assertEquals(['App\Middleware\Dummy','App\Middleware\Test'], $router->getMiddlewares());
    }

    public function testHasMatch()
    {
        $collection = new RouteCollection($this->pattern);
        $collection->setContext($this->context);
        $collection->add(
            new Route(
                'GET',
                '/test/dummy/<str:name>/<int:id>',
                'App\Controller\DefaultController::dummy',
                '(?<name>\w+).example.com',
                'http'
            )
        );
        $router = new Router($collection);
        $router->matchRequest();
        $this->assertTrue($router->hasMatch());
    }

    public function testGetMatchedRoute()
    {
        $collection = new RouteCollection($this->pattern);
        $collection->setContext($this->context);
        $collection->add(
            new Route(
                'GET',
                '/test/dummy/<str:name>/<int:id>',
                'App\Controller\DefaultController::dummy',
                '(?<name>\w+).example.com',
                'http'
            )
        );
        $router = new Router($collection);
        $router->matchRequest();
        $route = $router->getMatchedRoute();
        $this->assertEquals('/test/dummy/(?<name>\w+)/(?<id>\d+)/', $route->getPath());
    }

    public function testGetHostMatches()
    {
        $collection = new RouteCollection($this->pattern);
        $collection->setContext($this->context);
        $collection->add(
            new Route(
                'GET',
                '/test/dummy/<str:name>/<int:id>',
                'App\Controller\DefaultController::dummy',
                'test.example.com',
                'http'
            )
        );
        $router = new Router($collection);
        $router->matchRequest();
        $this->assertEquals('test.example.com', $router->getHostMatches()[0]);

        $collection = new RouteCollection($this->pattern);
        $collection->setContext($this->context);
        $collection->add(
            new Route(
                'GET',
                '/test/dummy/<str:name>/<int:id>',
                'App\Controller\DefaultController::dummy',
                '<str:name>.example.com',
                'http'
            )
        );
        $router = new Router($collection);
        $router->matchRequest();
        $this->assertEquals('test', $router->getHostMatches()['name']);
    }

    public function testGetCollection()
    {
        $collection = new RouteCollection($this->pattern);
        $collection->setContext($this->context);
        $collection->add(
            new Route(
                'GET',
                '/test/dummy/<str:name>/<int:id>',
                'App\Controller\DefaultController::dummy',
                'test.example.com',
                'http'
            )
        );
        $router = new Router($collection);
        $this->assertInstanceOf('Obullo\Router\RouteCollection', $router->getCollection());
    }

    public function testUrl()
    {
        $collection = new RouteCollection($this->pattern);
        $collection->setContext($this->context);
        $collection->add(
            new Route(
                'GET',
                '/test/dummy/<str:name>/<int:id>',
                'App\Controller\DefaultController::dummy',
                'test.example.com',
                'http'
            )
        );
        $router = new Router($collection);
        $dummyUrl = $router->url('/test/dummy/<str:name>/<int:id>', ['test',5]);
        $this->assertEquals($dummyUrl, '/test/dummy/test/5');
    }
}
