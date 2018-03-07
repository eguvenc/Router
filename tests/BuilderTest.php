<?php

use Obullo\Router\Builder;
use Obullo\Router\RequestContext;
use Obullo\Router\RouteCollection;
use Obullo\Router\Loader\YamlFileLoader;
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
class BuilderTest extends PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $request = Zend\Diactoros\ServerRequestFactory::fromGlobals();
        $loader = new YamlFileLoader;
        $file = dirname(__DIR__).'/tests/Resources/routes.yaml';
        $this->routes = $loader->load($file)->all();

        $configArray = array(
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
        $config = new Zend\Config\Config($configArray);
        $context = new RequestContext;
        $context->fromRequest($request);

        $collection = new RouteCollection($config);
        $collection->setContext($context);

        $this->builder = new Builder($collection);
    }

    public function testBuild()
    {
        $collection = $this->builder->build($this->routes);
        $this->assertInstanceOf('Obullo\Router\RouteCollection', $collection);

        $dummyRoute = $collection->get('dummy');
        $this->assertEquals('App\Controller\DefaultController::dummy', $dummyRoute->getHandler());
        $this->assertEquals('/(?<locale>[a-z]{2})/dummy/(?<name>\w+)', $dummyRoute->getPattern());
        /**
         * Render pipes
         */
        foreach ($collection->getPipes() as $pipe) {
            foreach ($pipe->getRoutes() as $name => $route) {
                $collection->add($name, $route);
            }
        }
        $userRoute = $collection->get('user/dummy');
        $this->assertEquals('App\Controller\DefaultController::dummy', $userRoute->getHandler());
        $this->assertEquals('/user/dummy/(?<name>\w+)/(?<id>\d+)', $userRoute->getPattern());
        $this->assertEquals('App\Middleware\Dummy', $userRoute->getStack()[0]);

        $testRoute = $collection->get('test_host/dummy');
        $this->assertEquals('/test_host/dummy/(?<name>\w+)/(?<id>\d+)', $testRoute->getPattern());
        $this->assertEquals('(?<name>\w+).example.com', $testRoute->getHost());
        $this->assertEquals(['http','https'], $testRoute->getSchemes());
    }
}