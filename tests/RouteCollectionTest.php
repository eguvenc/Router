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

class RouteCollectionTest extends PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $request = Laminas\Diactoros\ServerRequestFactory::fromGlobals();
        $pattern = new Pattern(
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
        $context = new RequestContext;
        $context->setPath('/dummy/test');
        $context->setMethod('GET');
        $context->setHost('test.example.com');
        $context->setScheme('https');

        $collection = new RouteCollection($pattern);
        $collection->setContext($context);

        $this->collection = $collection;
    }

    public function testAdd()
    {
        $route = new Route(
            ['GET','POST'],
            '/dummy/<str:name>/<int:id>',
            'App\Controller\DefaultController:index',
            '<str:name>.example.com',
            ['http','https'],
            'App\Middleware\Dummy'
        );
        $this->collection->add($route);
        $r = $this->collection->get('/dummy/<str:name>/<int:id>');

        $this->assertEquals(['GET','POST'], $r->getMethods());
        $this->assertEquals('/dummy/(?<name>\w+)/(?<id>\d+)/', $r->getPath());
        $this->assertEquals('App\Controller\DefaultController:index', $r->getHandler());
        $this->assertEquals('(?<name>\w+).example.com', $r->getHost());
        $this->assertEquals(['http','https'], $r->getSchemes());
        $this->assertEquals(['App\Middleware\Dummy'], $r->getMiddlewares());
    }

    public function testCount()
    {
        $first = new Route(
            ['GET','POST'],
            '/dummy/<str:name>/<int:id>',
            'App\Controller\DefaultController:index',
            '<str:name>.example.com',
            ['http','https'],
            'App\Middleware\Dummy'
        );
        $second = new Route(
            ['GET','POST'],
            '/dummy/<str:name>/<int:id>/second',
            'App\Controller\DefaultController:index',
            '<str:name>.example.com',
            ['http','https'],
            'App\Middleware\Dummy'
        );
        $this->collection->add($first);
        $this->collection->add($second);
        $this->assertEquals(2, $this->collection->count());
    }

    public function testAll()
    {
        $route = new Route(
            ['GET','POST'],
            '/dummy/<str:name>/<int:id>',
            'App\Controller\DefaultController:index',
            '<str:name>.example.com',
            ['http','https'],
            'App\Middleware\Dummy'
        );
        $this->collection->add($route);
        $r = $this->collection->all();
        $this->assertEquals('App\Controller\DefaultController:index', $r['/dummy/<str:name>/<int:id>']->getHandler());
    }

    public function testGetIterator()
    {
        $this->assertInstanceOf('ArrayIterator', $this->collection->getIterator());
    }

    public function testGetTypes()
    {
        $types = $this->collection->getPattern()->getTaggedTypes();
        $this->assertEquals('<int:id>', $types['id']->getPattern());
        $this->assertEquals('<str:name>', $types['name']->getPattern());
        $this->assertEquals('<str:word>', $types['word']->getPattern());
        $this->assertEquals('<any:any>', $types['any']->getPattern());
        $this->assertEquals('<bool:status>', $types['status']->getPattern());
        $this->assertEquals('<int:page>', $types['page']->getPattern());
        $this->assertEquals('<locale:locale>', $types['locale']->getPattern());
    }

    public function testGet()
    {
        $route = new Route(
            ['GET','POST'],
            '/dummy/<str:name>/<int:id>',
            'App\Controller\DefaultController:index',
            '<str:name>.example.com',
            ['http','https'],
            'App\Middleware\Dummy'
        );
        $this->collection->add($route);
        $r = $this->collection->get('/dummy/<str:name>/<int:id>');
        $this->assertEquals(['GET','POST'], $r->getMethods());
    }

    public function testRemove()
    {
        $first = new Route(
            ['GET','POST'],
            '/dummy/<str:name>/<int:id>',
            'App\Controller\DefaultController:index',
            '<str:name>.example.com',
            ['http','https'],
            'App\Middleware\Dummy'
        );
        $this->collection->add($first);
        $second = new Route(
            ['GET','POST'],
            '/dummy/<str:name>/<int:id>/second',
            'App\Controller\DefaultController:index',
            '<str:name>.example.com',
            ['http','https'],
            'App\Middleware\Dummy'
        );
        $this->collection->add($second);

        $this->collection->remove('/dummy/<str:name>/<int:id>/second');
        $this->assertFalse($this->collection->get('/dummy/<str:name>/<int:id>/second'));
    }

    public function testAddVariable()
    {
        $route = new Route(
            ['GET','POST'],
            '/dummy/test',
            'App\Controller\DefaultController:index',
            '<str:name>.example.com',
            ['http','https'],
            '$test'
        );
        $this->collection->add($route);
        $this->collection->addVariable('$test', ['middleware' => ['App\Middleware\Dummy', 'App\Middleware\Test']]);
        $var = $this->collection->getVariable('test');

        $this->assertEquals(['middleware' => ['App\Middleware\Dummy', 'App\Middleware\Test']], $var);

        $router = new Router($this->collection);
        $route = $router->popRoute();

        $this->assertEquals('/dummy/test', $route->getName());
        $this->assertEquals(['$test'], $router->getMiddlewares());
    }
}
