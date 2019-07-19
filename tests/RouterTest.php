<?php

use Obullo\Router\{
    Pipe,
    Route,
    Router,
    RequestContext,
    RouteCollection
};
use Obullo\Router\Types\{
    StrType,
    IntType,
    BoolType,
    SlugType,
    AnyType,
    TranslationType
};
class RouterTest extends PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $request = Zend\Diactoros\ServerRequestFactory::fromGlobals();
        $config = array(
            'patterns' => [
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
        $this->config = $config;
        $this->context = new RequestContext;
        $this->context->setPath('/test/dummy/test/55');
        $this->context->setMethod('GET');
        $this->context->setHost('test.example.com');
        $this->context->setScheme('http');
    }

    public function testPopRoute()
    {
        $collection = new RouteCollection($this->config);
        $collection->setContext($this->context);
        $collection->add(
            '/test/dummy/<str:name>/<int:id>',
            new Route([
                'method' => 'GET',
                'handler' => 'App\Controller\DefaultController::dummy',
                'middleware' => [],
                'test.example.com',
                'http'
            ])
        );
        $router = new Router($collection);
        $route = $router->popRoute();
        $this->assertEquals('/test/dummy/<str:name>/<int:id>', $route->getName());
        $this->assertEquals('/test/dummy/(?<name>\w+)/(?<id>\d+)/', $route->getPattern());
        $this->assertEquals('App\Controller\DefaultController::dummy', $route->getHandler());
    }

    public function testMatch()
    {
        $collection = new RouteCollection($this->config);
        $collection->setContext($this->context);
        $collection->add(
            '/dummy/<str:name>/<int:id>',
            new Route([
                'method' => 'GET',
                'handler' => 'App\Controller\DefaultController::dummy',
                'middleware' => [],
                'host' => '(?<name>\w+).example.com',
                'scheme' => 'https'
            ])
        );
        $router = new Router($collection);
        $route = $router->match('/dummy/test/55/','admin.example.com','https');
        $args = $route->getArguments();
        $this->assertEquals('test', $args['name']);
        $this->assertEquals('55', $args['id']);
        $this->assertEquals('/dummy/(?<name>\w+)/(?<id>\d+)/', $route->getPattern());
        $this->assertEquals('admin', $router->getHostMatches()['name']);
    }

    public function testMatchRequest()
    {
        $collection = new RouteCollection($this->config);
        $collection->setContext($this->context);
        $collection->add(
            '/test/dummy/<str:name>/<int:id>',
            new Route([
                'method' => 'GET',
                'handler' => 'App\Controller\DefaultController::dummy',
                'middleware' => [],
                'host' => '(?<name>\w+).example.com',
                'scheme' => 'http'
            ])
        );
        $router = new Router($collection);
        $route = $router->matchRequest();
        $args = $route->getArguments();
        $this->assertEquals('test', $args['name']);
        $this->assertEquals('55', $args['id']);
        $this->assertEquals('/test/dummy/(?<name>\w+)/(?<id>\d+)/', $route->getPattern());
        $this->assertEquals('test', $router->getHostMatches()['name']);
    }

    public function testGetStack()
    {
        $collection = new RouteCollection($this->config);
        $collection->setContext($this->context);
        $collection->add(
            '/test/dummy/<str:name>/<int:id>',
            new Route(
                [
                    'method' => 'GET',
                    'handler' => 'App\Controller\DefaultController::dummy',
                    'middleware' => ['App\Middleware\Dummy','App\Middleware\Test'],
                    'host' => '(?<name>\w+).example.com',
                    'scheme' => 'http'
                ]
            )
        );
        $router = new Router($collection);
        $router->matchRequest();
        $this->assertEquals(['App\Middleware\Dummy','App\Middleware\Test'], $router->getStack());
    }

    public function testHasMatch()
    {
        $collection = new RouteCollection($this->config);
        $collection->setContext($this->context);
        $collection->add(
            '/test/dummy/<str:name>/<int:id>',
            new Route([
                'method' => 'GET',
                'handler' => 'App\Controller\DefaultController::dummy',
                'middleware' => [],
                'host' => '(?<name>\w+).example.com',
                'scheme' => 'http'
            ])
        );
        $router = new Router($collection);
        $router->matchRequest();
        $this->assertTrue($router->hasMatch());
    }

    public function testGetMatchedRoute()
    {
        $collection = new RouteCollection($this->config);
        $collection->setContext($this->context);
        $collection->add(
            '/test/dummy/<str:name>/<int:id>',
            new Route([
                'method' => 'GET',
                'handler' => 'App\Controller\DefaultController::dummy',
                'host' => '(?<name>\w+).example.com',
                'scheme' => 'http'
            ])
        );
        $router = new Router($collection);
        $router->matchRequest();
        $route = $router->getMatchedRoute();
        $this->assertEquals('/test/dummy/(?<name>\w+)/(?<id>\d+)/', $route->getPattern());
    }

    public function testGetHostMatches()
    {
        $collection = new RouteCollection($this->config);
        $collection->setContext($this->context);
        $collection->add(
            '/test/dummy/<str:name>/<int:id>',
            new Route(
                [
                    'method' => 'GET',
                    'handler' => 'App\Controller\DefaultController::dummy',
                    'host' => 'test.example.com',
                    'scheme' => 'http'
                ]
            )
        );
        $router = new Router($collection);
        $router->matchRequest();
        $this->assertEquals('test.example.com', $router->getHostMatches()[0]);

        $collection = new RouteCollection($this->config);
        $collection->setContext($this->context);
        $collection->add(
            '/test/dummy/<str:name>/<int:id>',
            new Route(
                [
                    'method' => 'GET',
                    'handler' => 'App\Controller\DefaultController::dummy',
                    'host' => '(?<name>\w+).example.com',
                    'scheme' => 'http'
                ]
            )
        );
        $router = new Router($collection);
        $router->matchRequest();
        $this->assertEquals('test', $router->getHostMatches()['name']);
    }

    public function testGetCollection()
    {
        $collection = new RouteCollection($this->config);
        $collection->setContext($this->context);
        $collection->add(
            '/test/dummy/<str:name>/<int:id>',
            new Route(
                [
                    'method' => 'GET',
                    'handler' => 'App\Controller\DefaultController::dummy',
                    'host' => 'test.example.com',
                    'scheme' => 'http'
                ]
            )
        );
        $router = new Router($collection);
        $this->assertInstanceOf('Obullo\Router\RouteCollection', $router->getCollection());
    }

    public function testUrl()
    {
        $collection = new RouteCollection($this->config);
        $collection->setContext($this->context);
        $collection->add(
            '/test/dummy/<str:name>/<int:id>',
            new Route(
                [
                    'method' => 'GET',
                    'handler' => 'App\Controller\DefaultController::dummy',
                    'host' => 'test.example.com',
                    'scheme' => 'http'
                ]
            )
        );
        $router = new Router($collection);
        $dummyUrl = $router->url('/test/dummy/<str:name>/<int:id>', ['name' => 'test', 'id' => 5]);
        $this->assertEquals($dummyUrl, '/test/dummy/test/5');
    }
}