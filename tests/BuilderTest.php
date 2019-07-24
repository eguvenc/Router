<?php

use Obullo\Router\{
    Pattern,
    Builder,
    RequestContext,
    RouteCollection
};
use Obullo\Router\Types\{
    StrType,
    IntType,
    BoolType,
    SlugType,
    AnyType,
    FourDigitYearType,
    TwoDigitMonthType,
    TwoDigitDayType,
    TranslationType
};
use Symfony\Component\Yaml\Yaml;

class BuilderTest extends PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $request = Zend\Diactoros\ServerRequestFactory::fromGlobals();
        $file = dirname(__DIR__).'/tests/Resources/routes.yaml';
        $this->routes = Yaml::parseFile($file);

        $pattern = new Pattern([
                new IntType('<int:id>'),  // \d+
                new StrType('<str:name>'),     // \w+
                new StrType('<str:word>'),     // \w+
                new AnyType('<any:any>'),
                new BoolType('<bool:status>'),
                new IntType('<int:page>'),
                new SlugType('<slug:slug>'),
                new TranslationType('<locale:locale>'),
        ]);
        $context = new RequestContext;
        $context->fromRequest($request);

        $collection = new RouteCollection($pattern);
        $collection->setContext($context);

        $this->builder = new Builder($collection);
    }

    public function testBuild()
    {
        $collection = $this->builder->build($this->routes);
        $this->assertInstanceOf('Obullo\Router\RouteCollection', $collection);

        $dummyRoute = $collection->get('/<locale:locale>/dummy/<str:name>');
        $this->assertEquals('App\Controller\DefaultController::dummy', $dummyRoute->getHandler());
        $this->assertEquals('/(?<locale>[a-z]{2})/dummy/(?<name>\w+)/', $dummyRoute->getPath());

        $testRoute = $collection->get('/dummy/<str:name>/<int:id>');
        $this->assertEquals('/dummy/(?<name>\w+)/(?<id>\d+)/', $testRoute->getPath());
        $this->assertEquals('<str:name>.example.com', $testRoute->getHost());
        $this->assertEquals(['http','https'], $testRoute->getSchemes());
    }
}