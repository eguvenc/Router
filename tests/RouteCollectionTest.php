<?php

use Obullo\Router\{
    Pipe,
    Route,
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
class RouteCollectionTest extends PHPUnit_Framework_TestCase
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
        $context = new RequestContext;
        $context->setPath('/dummy/test');
        $context->setMethod('GET');
        $context->setHost('test.example.com');
        $context->setScheme('https');

        $collection = new RouteCollection($config);
        $collection->setContext($context);

        $this->collection = $collection;
    }

    public function testAddPipe()
    {
        $pipe = new Pipe('test/',[
            'middleware' => 'App\Middleware\Dummy',
            'host' => '<str:name>.example.com',
            'scheme' => ['http','https'],
            '$variable' => 'test attribute'
        ]);
        $this->collection->addPipe($pipe);
        $p = $this->collection->getPipes()[0];

        $this->assertInstanceOf('Obullo\Router\Pipe', $p);
        $this->assertEquals('(?<name>\w+).example.com', $p->getHost());
        $this->assertEquals('/test/', $p->getPipe());
        $this->assertEquals('test attribute', $p->getAttribute('variable'));
    }

    public function testAdd()
    {
        $route = new Route(
            [
                'method' => ['GET','POST'],
                'path' => '/dummy/<str:name>/<int:id>',
                'handler' => 'App\Controller\DefaultController:index',
                'middleware' => 'App\Middleware\Dummy',
                'host' => '<str:name>.example.com',
                'scheme' => ['http','https'],
                '$variable' => 'test attribute'
            ]
        );
        $this->collection->add('dummy', $route);
        $r = $this->collection->get('dummy');

        $this->assertEquals(['GET','POST'], $r->getMethods());
        $this->assertEquals('/dummy/(?<name>\w+)/(?<id>\d+)/', $r->getPattern());
        $this->assertEquals('App\Controller\DefaultController:index', $r->getHandler());
        $this->assertEquals('(?<name>\w+).example.com', $r->getHost());
        $this->assertEquals(['http','https'], $r->getSchemes());
        $this->assertEquals(['App\Middleware\Dummy'], $r->getStack());
        $this->assertEquals('test attribute', $r->getAttribute('variable'));
    }

    public function testCount()
    {
        $route = new Route(
            [
                'method' => ['GET','POST'],
                'path' => '/dummy/<str:name>/<int:id>',
                'handler' => 'App\Controller\DefaultController:index',
                'middleware' => 'App\Middleware\Dummy',
                'host' => '<str:name>.example.com',
                'scheme' => ['http','https']
            ]
        );
        $this->collection->add('dummy', $route);
        $this->collection->add('dummy2', $route);
        $this->assertEquals(2, $this->collection->count());
    }

    public function testAll()
    {
        $route = new Route(
            [
                'method' => ['GET','POST'],
                'path' => '/dummy/<str:name>/<int:id>',
                'handler' => 'App\Controller\DefaultController:index',
                'middleware' => 'App\Middleware\Dummy',
                'host' =>  '<str:name>.example.com',
                'scheme' => ['http','https']
            ]  
        );
        $this->collection->add('dummy', $route);
        $r = $this->collection->all();
        $this->assertEquals('App\Controller\DefaultController:index', $r['dummy']->getHandler());
    }

    public function testGetPipes()
    {
        $pipe = new Pipe('test/',
            [
                'middleware' => 'App\Middleware\Dummy',
                'host' => '<str:name>.example.com',
                'method' => ['http','https']
            ]
        );
        $this->collection->addPipe($pipe);
        $this->assertInstanceOf('Obullo\Router\Pipe', $this->collection->getPipes()[0]);
    }

    public function testGetIterator()
    {
        $this->assertInstanceOf('ArrayIterator', $this->collection->getIterator());
    }

    public function testGetTypes()
    {
        $types = $this->collection->getPatterns();
        $this->assertEquals('<int:id>', $types['id']->getType());
        $this->assertEquals('<str:name>', $types['name']->getType());
        $this->assertEquals('<str:word>', $types['word']->getType());
        $this->assertEquals('<any:any>', $types['any']->getType());
        $this->assertEquals('<bool:status>', $types['status']->getType());
        $this->assertEquals('<int:page>', $types['page']->getType());
        $this->assertEquals('<locale:locale>', $types['locale']->getType());
    }

    public function testGet()
    {
        $route = new Route(
            [
                'method' => ['GET','POST'],
                'path' => '/dummy/<str:name>/<int:id>',
                'handler' => 'App\Controller\DefaultController:index',
                'middleware' => 'App\Middleware\Dummy',
                'host' =>  '<str:name>.example.com',
                'scheme' => ['http','https']
            ]  
        );
        $this->collection->add('dummy', $route);
        $r = $this->collection->get('dummy');
        $this->assertEquals(['GET','POST'], $r->getMethods());
    }

    public function testRemove()
    {
        $route = new Route(
            [
                'method' => ['GET','POST'],
                'path' => '/dummy/<str:name>/<int:id>',
                'handler' => 'App\Controller\DefaultController:index',
                'middleware' => 'App\Middleware\Dummy',
                'host' =>  '<str:name>.example.com',
                'scheme' => ['http','https']
            ]
        );
        $this->collection->add('dummy', $route);
        $this->collection->add('dummy2', $route);

        $this->collection->remove('dummy2');
        $this->assertFalse($this->collection->get('dummy2'));
    }

    public function testFormatPattern()
    {
        $this->assertEquals('(?<id>\d+)', $this->collection->formatPattern('<int:id>'));
        $this->assertEquals('(?<name>\w+)', $this->collection->formatPattern('<str:name>'));
    }
}