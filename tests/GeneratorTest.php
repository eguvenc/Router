<?php

use Obullo\Router\Pattern;
use Obullo\Router\Route;
use Obullo\Router\RequestContext;
use Obullo\Router\RouteCollection;
use Obullo\Router\TranslatableRouteCollection;
use Obullo\Router\Generator;
use Obullo\Router\Types\StrType;
use Obullo\Router\Types\IntType;
use Obullo\Router\Types\BoolType;
use Obullo\Router\Types\SlugType;
use Obullo\Router\Types\AnyType;
use Obullo\Router\Types\FourDigitYearType;
use Obullo\Router\Types\TwoDigitMonthType;
use Obullo\Router\Types\TwoDigitDayType;
use Obullo\Router\Types\TranslationType;
use Zend\I18n\Translator\Translator;

class GeneratorTest extends PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->pattern = new Pattern([
                new IntType('<int:id>'),
                new StrType('<str:name>'),
                new StrType('<str:word>'),
                new AnyType('<any:any>'),
                new BoolType('<bool:status>'),
                new IntType('<int:page>'),
                new SlugType('<slug:slug>'),
                new SlugType('<slug:slug_>', '(?<%s>[\w-_]+)'),
                new TranslationType('<locale:locale>'),
        ]);
        $this->collection = new RouteCollection($this->pattern);
        $this->collection->add(new Route('GET', '/<locale:locale>/dummy/<str:name>/<int:id>', 'App\Controller\DefaultController::dummy'));
        $this->collection->add(new Route('GET', '/slug/<slug:slug_>', 'App\Controller\DefaultController::dummy'));
        $this->collection->add(new Route('GET', '/test/me', 'App\Controller\DefaultController::dummy'));
    }

    public function testGenerate()
    {
        $dummy = (new Generator($this->collection))->generate('/<locale:locale>/dummy/<str:name>/<int:id>', ['en','test',5]);
        $slug  = (new Generator($this->collection))->generate('/slug/<slug:slug_>', ['abcd-123_']);
        $test  = (new Generator($this->collection))->generate('/test/me');

        $this->assertEquals($dummy, '/en/dummy/test/5');
        $this->assertEquals($slug, '/slug/abcd-123_');
        $this->assertEquals($test, '/test/me');
    }

    public function testTranslatableGenerate()
    {
        $this->collection = new TranslatableRouteCollection($this->pattern);
        
        $translator = new Translator;
        $translator->setLocale('tr');
        $translator->addTranslationFilePattern('PhpArray', ROOT, 'tests/Resources/%s/routing.php');

        $this->collection->setTranslator($translator);

        $this->collection->add(new Route('GET', '/<locale:locale>/{dummy}/<str:name>/<int:id>', 'App\Controller\DefaultController::dummy'));
        $this->collection->add(new Route('GET', '/{slug}/<slug:slug_>', 'App\Controller\DefaultController::dummy'));
        $this->collection->add(new Route('GET', '/{hello}/{world}', 'App\Controller\DefaultController::dummy'));

        $dummy = (new Generator($this->collection))->generate('/<locale:locale>/{dummy}/<str:name>/<int:id>', ['tr','test',5], 'tr');
        $slug  = (new Generator($this->collection))->generate('/{slug}/<slug:slug_>', ['abcd-123_'], 'tr');
        $hello  = (new Generator($this->collection))->generate('/{hello}/{world}', [], 'tr');

        $this->assertEquals($dummy, '/tr/aptal/test/5');
        $this->assertEquals($slug, '/slag/abcd-123_');
        $this->assertEquals($hello, '/merhaba/dunya');
    }
}
