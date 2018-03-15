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
            'types' => [
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

    public function testPopPipe()
    {
        $collection = new RouteCollection($this->config);
        $collection->setContext($this->context);

        $pipe = new Pipe('test/','App\Middleware\Dummy','<str:name>.example.com',['http','https']);
        $pipe->add('dummy', new Route('GET', '/dummy/<str:name>/<int:id>', 'App\Controller\DefaultController::test'));
        $collection->addPipe($pipe);

        $router = new Router($collection);
        $router->popPipe();
        $route = $collection->get('test/dummy');
        $this->assertEquals('test/dummy', $route->getName());
        $this->assertEquals('/test/dummy/(?<name>\w+)/(?<id>\d+)', $route->getPattern());
        $this->assertEquals('App\Controller\DefaultController::test', $route->getHandler());
    }

    public function testPopRoute()
    {
        $collection = new RouteCollection($this->config);
        $collection->setContext($this->context);
        $collection->add(
            'dummy',
            new Route('GET','/test/dummy/<str:name>/<int:id>','App\Controller\DefaultController::dummy',[],'test.example.com','http')
        );
        $router = new Router($collection);
        $router->popPipe();
        $route = $router->popRoute();
        $this->assertEquals('dummy', $route->getName());
        $this->assertEquals('/test/dummy/(?<name>\w+)/(?<id>\d+)', $route->getPattern());
        $this->assertEquals('App\Controller\DefaultController::dummy', $route->getHandler());
    }

    public function testMatch()
    {
        $collection = new RouteCollection($this->config);
        $collection->setContext($this->context);
        $collection->add(
            'dummy',
            new Route('GET','/dummy/<str:name>/<int:id>','App\Controller\DefaultController::dummy',[],'(?<name>\w+).example.com','https')
        );
        $router = new Router($collection);
        $route = $router->match('/dummy/test/55','admin.example.com','https');
        $args = $route->getArguments();
        $this->assertEquals('test', $args['name']);
        $this->assertEquals('55', $args['id']);
        $this->assertEquals('/dummy/(?<name>\w+)/(?<id>\d+)', $route->getPattern());
        $this->assertEquals('admin', $router->getHostMatches()['name']);
    }

    public function testMatchRequest()
    {
        $collection = new RouteCollection($this->config);
        $collection->setContext($this->context);
        $collection->add(
            'dummy',
            new Route('GET','/test/dummy/<str:name>/<int:id>','App\Controller\DefaultController::dummy',[],'(?<name>\w+).example.com','http')
        );
        $router = new Router($collection);
        $route = $router->matchRequest();
        $args = $route->getArguments();
        $this->assertEquals('test', $args['name']);
        $this->assertEquals('55', $args['id']);
        $this->assertEquals('/test/dummy/(?<name>\w+)/(?<id>\d+)', $route->getPattern());
        $this->assertEquals('test', $router->getHostMatches()['name']);
    }

    public function testGetStack()
    {
        $collection = new RouteCollection($this->config);
        $collection->setContext($this->context);
        $collection->add(
            'dummy',
            new Route(
                'GET',
                '/test/dummy/<str:name>/<int:id>',
                'App\Controller\DefaultController::dummy',
                ['App\Middleware\Dummy','App\Middleware\Test'],
                '(?<name>\w+).example.com',
                'http'
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
            'dummy',
            new Route('GET','/test/dummy/<str:name>/<int:id>','App\Controller\DefaultController::dummy',[],'(?<name>\w+).example.com','http')
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
            'dummy',
            new Route('GET','/test/dummy/<str:name>/<int:id>','App\Controller\DefaultController::dummy',[],'(?<name>\w+).example.com','http')
        );
        $router = new Router($collection);
        $router->matchRequest();
        $route = $router->getMatchedRoute();
        $this->assertEquals('/test/dummy/(?<name>\w+)/(?<id>\d+)', $route->getPattern());
    }

    public function testGetHostMatches()
    {
        $collection = new RouteCollection($this->config);
        $collection->setContext($this->context);
        $collection->add(
            'dummy',
            new Route('GET','/test/dummy/<str:name>/<int:id>','App\Controller\DefaultController::dummy',[],'test.example.com','http')
        );
        $router = new Router($collection);
        $router->matchRequest();
        $this->assertEquals('test.example.com', $router->getHostMatches()[0]);

        $collection = new RouteCollection($this->config);
        $collection->setContext($this->context);
        $collection->add(
            'dummy',
            new Route('GET','/test/dummy/<str:name>/<int:id>','App\Controller\DefaultController::dummy',[],'(?<name>\w+).example.com','http')
        );
        $router = new Router($collection);
        $router->matchRequest();
        $this->assertEquals('test', $router->getHostMatches()['name']);
    }
}