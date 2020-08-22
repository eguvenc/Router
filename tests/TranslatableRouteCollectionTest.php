<?php

use PHPUnit\Framework\TestCase;
use Obullo\Router\Route;
use Obullo\Router\RequestContext;
use Obullo\Router\TranslatableRouteCollection;
use Obullo\Router\Types\StrType;
use Obullo\Router\Types\IntType;
use Obullo\Router\Types\BoolType;
use Obullo\Router\Types\SlugType;
use Obullo\Router\Types\AnyType;
use Obullo\Router\Types\TranslationType;
use Laminas\I18n\Translator\Translator;

class TranslatableRouteCollectionTest extends TestCase
{
    public function setup() : void
    {
        $request = Laminas\Diactoros\ServerRequestFactory::fromGlobals();
        $config = array(
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
        $context = new RequestContext;
        $context->setPath('/dummy/test');
        $context->setMethod('GET');
        $context->setHost('test.example.com');
        $context->setScheme('https');

        $collection = new TranslatableRouteCollection($config);
        $collection->setContext($context);
        $this->collection = $collection;

        $this->translator = new Translator;
        $this->translator->setLocale('tr');
        $this->translator->addTranslationFilePattern('PhpArray', ROOT, 'tests/Resources/%s/routing.php');

        $this->collection->setTranslator($this->translator);
    }

    public function testSetTranslator()
    {
        $this->assertInstanceOf('Zend\I18n\Translator\Translator', $this->collection->getTranslator());
    }

    public function testSetTranslatorTextDomain()
    {
        $this->collection->setTranslatorTextDomain('test');
        $this->assertEquals('test', $this->collection->getTranslatorTextDomain());
    }

    public function testTranslatePath()
    {
        $data = $this->collection->translatePath('/{hello}/{world}/<str:name>/test');

        $this->assertEquals('/merhaba/dunya/<str:name>/test', $data['path']);
    }

    public function testTranslatePathException()
    {
        try {
            $this->collection->translatePath('/{hello}/{me}/<str:name>/test');
        } catch (Exception $e) {
            $this->assertInstanceOf('Obullo\Router\Exception\PathTranslationException', $e);
            $this->assertEquals("No route translation found corresponding to item '{me}'.", $e->getMessage());
        }
    }

    public function testAdd()
    {
        $this->collection->add('locale.dummy.name.id', new Route('GET', '/<locale:locale>/{dummy}/<str:name>/<int:id>', 'App\Controller\DefaultController::dummy'));
        $this->collection->add('slug.slug', new Route('GET', '/{slug}/<slug:slug_>', 'App\Controller\DefaultController::dummy'));
        $this->collection->add('hello.world', new Route('GET', '/{hello}/{world}', 'App\Controller\DefaultController::dummy'));

        $route = $this->collection->get('/<locale:locale>/{dummy}/<str:name>/<int:id>');
        $this->assertEquals($route->getName(), '/<locale:locale>/{dummy}/<str:name>/<int:id>');

        $data = $this->collection->translatePath($route->getName(), 'tr');
        $this->assertEquals($data['path'], '/<locale:locale>/aptal/<str:name>/<int:id>');

        $data = $this->collection->translatePath($route->getName(), 'en');
        $this->assertEquals($data['path'], '/<locale:locale>/dummy/<str:name>/<int:id>');

        $route = $this->collection->get('/{slug}/<slug:slug_>');
        $this->assertEquals($route->getName(), '/{slug}/<slug:slug_>');

        $data = $this->collection->translatePath($route->getName(), 'tr');
        $this->assertEquals($data['path'], '/slag/<slug:slug_>');

        $data = $this->collection->translatePath($route->getName(), 'en');
        $this->assertEquals($data['path'], '/slug/<slug:slug_>');

        $route = $this->collection->get('/{hello}/{world}');
        $this->assertEquals($route->getName(), '/{hello}/{world}');

        $data = $this->collection->translatePath($route->getName(), 'tr');
        $this->assertEquals($data['path'], '/merhaba/dunya');

        $data = $this->collection->translatePath($route->getName(), 'en');
        $this->assertEquals($data['path'], '/hello/world');
    }
}
