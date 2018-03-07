<?php

use Obullo\Router\{
    Loader\YamlFileLoader,
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
class YamlFileLoaderTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->loader = new YamlFileLoader;
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
                new TranslationType('<locale:locale>', '(?<%s>(en|de|es))'),
            ]
        );
        $request = Zend\Diactoros\ServerRequestFactory::fromGlobals();
        $this->config = new Zend\Config\Config($configArray);
        $this->context = new RequestContext;
        $this->context->fromRequest($request);
    }

    public function testLoad()
    {
        $file = dirname(__DIR__).'/Resources/routes.yaml';
        $routes = $this->loader->load($file)->all();

        $this->assertArrayHasKey('home', $routes);
        $this->assertArrayHasKey('dummy', $routes);
        $this->assertArrayHasKey('user/', $routes);
    }

    public function testBuild()
    {
        $file = dirname(__DIR__).'/Resources/routes.yaml';
        $this->loader->load($file);

        $collection = new RouteCollection($this->config);
        $collection->setContext($this->context);
        $collection = $this->loader->build($collection);

        //------------------ Pipes -------------------------//
        //
        $pipes = $collection->getPipes();
        $user  = $pipes[0];
        $host  = $pipes[1];

        $this->assertEquals('user/', $user->getPipe());
        $this->assertEquals('test_host/', $host->getPipe());

        $this->assertEquals('App\Middleware\Dummy', $user->getStack()[0]);
        $this->assertEquals(null, $user->getHost());
        $this->assertEquals(array(), $user->getSchemes());

        $this->assertEquals('(?<name>\w+).example.com', $host->getHost());
        $this->assertEquals('http', $host->getSchemes()[0]);
        $this->assertEquals('https', $host->getSchemes()[1]);

        foreach ($pipes as $pipe) {
            foreach ($pipe->getRoutes() as $name => $route) {
                $collection->add($name, $route);
            }
        }
        //------------------ Routes -------------------------//
        //
        $routes = $collection->all();

        $this->assertArrayHasKey('home', $routes);
        $this->assertArrayHasKey('dummy', $routes);
        $this->assertArrayHasKey('test_host', $routes);
        $this->assertArrayHasKey('user/dummy', $routes);
        $this->assertArrayHasKey('user/lucky', $routes);
        $this->assertArrayHasKey('test_host/dummy', $routes);

        $this->assertEquals('App\Middleware\Dummy', $routes['home']->getStack()[0]);
        $this->assertEquals('GET', $routes['home']->getMethods()[0]);
        $this->assertEquals('App\Controller\DefaultController::index', $routes['home']->getHandler());
        $this->assertEquals('/', $routes['home']->getPattern());
        $this->assertEquals(null, $routes['home']->getHost());
        $this->assertEquals(array(), $routes['home']->getSchemes());

        $this->assertEquals('GET', $routes['dummy']->getMethods()[0]);
        $this->assertEquals('App\Controller\DefaultController::dummy', $routes['dummy']->getHandler());
        $this->assertEquals('/(?<locale>(en|de|es))/dummy/(?<name>\w+)', $routes['dummy']->getPattern());
        $this->assertEquals(null, $routes['dummy']->getHost());
        $this->assertEquals(array(), $routes['dummy']->getSchemes());

        $this->assertEquals('test_host', $routes['test_host']->getName());
        $this->assertEquals('(?<name>\w+).example.com', $routes['test_host']->getHost());
        $this->assertEquals('http', $routes['test_host']->getSchemes()[0]);
        $this->assertEquals('https', $routes['test_host']->getSchemes()[1]);
        $this->assertEquals('App\Controller\DefaultController::dummy', $routes['test_host']->getHandler());
        $this->assertEquals('/dummy/(?<name>\w+)/(?<id>\d+)', $routes['test_host']->getPattern());

        $this->assertEquals('App\Middleware\Dummy', $routes['user/dummy']->getStack()[0]);
        $this->assertEquals('GET', $routes['user/dummy']->getMethods()[0]);
        $this->assertEquals('App\Controller\DefaultController::dummy', $routes['user/dummy']->getHandler());
        $this->assertEquals('/user/dummy/(?<name>\w+)/(?<id>\d+)', $routes['user/dummy']->getPattern());
        $this->assertEquals(null, $routes['user/dummy']->getHost());
        $this->assertEquals(array(), $routes['user/dummy']->getSchemes());

        $this->assertEquals('GET', $routes['user/lucky']->getMethods()[0]);
        $this->assertEquals('App\Controller\DefaultController::lucky', $routes['user/lucky']->getHandler());
        $this->assertEquals('/user/lucky/(?<name>\w+)/(?<slug>[\w-]+)', $routes['user/lucky']->getPattern());
        $this->assertEquals(null, $routes['user/lucky']->getHost());
        $this->assertEquals(array(), $routes['user/lucky']->getSchemes());

        $this->assertEquals('GET', $routes['test_host/dummy']->getMethods()[0]);
        $this->assertEquals('App\Controller\DefaultController::dummy', $routes['test_host/dummy']->getHandler());
        $this->assertEquals('/test_host/dummy/(?<name>\w+)/(?<id>\d+)', $routes['test_host/dummy']->getPattern());
        $this->assertEquals('(?<name>\w+).example.com', $routes['test_host/dummy']->getHost());
        $this->assertEquals(array('http', 'https'), $routes['test_host/dummy']->getSchemes());
    }
}