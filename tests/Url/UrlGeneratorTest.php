<?php

use Obullo\Router\{
    Route,
    RequestContext,
    RouteCollection,
    Url\UrlGenerator
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
class UrlGeneratorTest extends PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $configArray = array(
            'types' => [
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
        $this->config = new Zend\Config\Config($configArray);
        $this->context = new RequestContext;
        $this->context->fromRequest($request);

        $this->collection = new RouteCollection($this->config, $this->context);
        $this->collection->add('dummy', new Route('GET', '/<locale:locale>/dummy/<str:name>/<int:id>', 'App\Controller\DefaultController::dummy'));
        $this->collection->add('dummy2', new Route('GET', '/dummy2/<slug:slug_>', 'App\Controller\DefaultController::dummy'));
        $this->collection->add('test', new Route('GET', '/test/me', 'App\Controller\DefaultController::dummy'));
    }

    public function testGenerate()            
    {
        $url = new UrlGenerator($this->collection);
        $str = $url->generate('dummy', ['locale' => 'en', 'name' => 'test', 'id' => 5]);
        $str2 = $url->generate('dummy2', ['slug_' => 'abcd-123_']);
        $test = $url->generate('test');

        $this->assertEquals($str, '/en/dummy/test/5');
        $this->assertEquals($str2, '/dummy2/abcd-123_');
        $this->assertEquals($test, '/test/me');
    }
}