<?php

use Obullo\Router\{
    Route,
    RequestContext,
    RouteCollection,
    Generator
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
class GeneratorTest extends PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $configArray = array(
            'patterns' => [
                new IntType('<int:id>'),
                new StrType('<str:name>'),
                new StrType('<str:word>'),
                new AnyType('<any:any>'),
                new BoolType('<bool:status>'),
                new IntType('<int:page>'),
                new SlugType('<slug:slug>'),
                new SlugType('<slug:slug_>', '(?<%s>[\w-_]+)'),
                new TranslationType('<locale:locale>'),
            ]
        );
        $request = Zend\Diactoros\ServerRequestFactory::fromGlobals();
        $this->config  = $configArray;
        $this->context = new RequestContext;
        $this->context->fromRequest($request);

        $this->collection = new RouteCollection($this->config, $this->context);
        $route = [
            'method' =>  'GET',
            'handler' => 'App\Controller\DefaultController::dummy'
        ];
        $this->collection->add('/<locale:locale>/dummy/<str:name>/<int:id>', new Route($route));
        $route = [
            'method' => 'GET',
            'handler' => 'App\Controller\DefaultController::dummy'
        ];
        $this->collection->add('/slug/<slug:slug_>', new Route($route));
        $route = [
            'method' => 'GET',
            'handler' => 'App\Controller\DefaultController::dummy'
        ];
        $this->collection->add('/test/me', new Route($route));
    }

    public function testGenerate()            
    {
        $dummy = (new Generator($this->collection))->generate('/<locale:locale>/dummy/<str:name>/<int:id>', ['locale' => 'en', 'name' => 'test', 'id' => 5]);
        $slug  = (new Generator($this->collection))->generate('/slug/<slug:slug_>', ['slug_' => 'abcd-123_']);
        $test  = (new Generator($this->collection))->generate('/test/me');

        $this->assertEquals($dummy, '/en/dummy/test/5');
        $this->assertEquals($slug, '/slug/abcd-123_');
        $this->assertEquals($test, '/test/me');
    }
}