<?php

use PHPUnit\Framework\TestCase;
use Obullo\Router\Builder;
use Obullo\Router\RequestContext;
use Obullo\Router\RouteCollection;
use Obullo\Router\Types\StrType;
use Obullo\Router\Types\IntType;
use Obullo\Router\Types\BoolType;
use Obullo\Router\Types\SlugType;
use Obullo\Router\Types\AnyType;
use Obullo\Router\Types\FourDigitYearType;
use Obullo\Router\Types\TwoDigitMonthType;
use Obullo\Router\Types\TwoDigitDayType;
use Obullo\Router\Types\TranslationType;
use Symfony\Component\Yaml\Yaml;

class BuilderTest extends TestCase
{
    public function setup() : void
    {
        $request = Laminas\Diactoros\ServerRequestFactory::fromGlobals();
        $file = dirname(__DIR__).'/tests/Resources/routes.yaml';
        $this->routes = Yaml::parseFile($file);

        $config = array(
            'types' => [
                new IntType('<int:id>'),  // \d+
                new StrType('<str:name>'),     // \w+
                new StrType('<str:word>'),     // \w+
                new AnyType('<any:any>'),
                new BoolType('<bool:status>'),
                new IntType('<int:page>'),
                new SlugType('<slug:slug>'),
                new TranslationType('<locale:locale>'),
            ]
        );
        $context = new RequestContext;
        $context->fromRequest($request);

        $collection = new RouteCollection($config);
        $collection->setContext($context);

        $this->builder = new Builder($collection);
        $this->collection = $this->builder->build($this->routes);
    }

    public function testBuild()
    {
        $this->assertInstanceOf('Obullo\Router\RouteCollection', $this->collection);

        $dummyRoute = $this->collection->get('/<locale:locale>/dummy/<str:name>');
        $this->assertEquals('App\Controller\DefaultController::dummy', $dummyRoute->getHandler());
        $this->assertEquals('/(?<locale>[a-z]{2})/dummy/(?<name>\w+)/', $dummyRoute->getPath());

        $testRoute = $this->collection->get('/dummy/<str:name>/<int:id>');
        $this->assertEquals('/dummy/(?<name>\w+)/(?<id>\d+)/', $testRoute->getPath());
        $this->assertEquals('<str:name>.example.com', $testRoute->getHost());
        $this->assertEquals(['http','https'], $testRoute->getSchemes());
    }

    public function testMiddlewareVariable()
    {
        $testRoute = $this->collection->get('/<locale:locale>/dummy/<str:name>');
        $middlewares = $testRoute->getMiddlewares();

        $this->assertEquals("App\Middleware\Var", $middlewares[0]);
        $this->assertEquals("App\Middleware\Test", $middlewares[1]);
        $this->assertEquals("App\Middleware\Locale", $middlewares[2]);
    }
}
