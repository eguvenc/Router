<?php

use Obullo\Router\RouteCollection;
use Obullo\Router\Pattern\{
    StrPattern,
    IntPattern,
    NumberPattern,
    AnyPattern,
    SlugPattern,
    BoolPattern
};
class RouteRuleTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $configArray = array(
            'patterns' => [
                new IntPattern('<id:int>'),  // \d+
                new StrPattern('<name:str>'),// \w+
                new StrPattern('<word:str>'),// \w+
                new AnyPattern('<any:str>'),
                new IntPattern('<page:int>'),
                new BoolPattern('<active:bool>'),
                new SlugPattern('<slug:str>'),
                new SlugPattern('<slug_:str>', '(?<$name>[\w-_]+)$'), // slug with underscore
                new NumberPattern('<price:float>', '(?<$name>[0-9.]+)') // number with float
            ]
        );
        $this->config = new Zend\Config\Config($configArray);

        $this->routeRule = new Obullo\Router\RouteRule(
            'GET',
            'welcome/index/([a-z]+)/(\d+)',
            'WelcomeController->index'
        );
        $this->request = $this->createRequest("http://example.com/welcome/index/abc/10");
    }

    public function testRoot()
    {
        $this->routeRule->setRoot("welcome/");
        $this->assertEquals("welcome/", $this->routeRule->getRoot());
    }

    public function testGetMethods()
    {
        $methods = $this->routeRule->getMethods();
        $this->assertEquals("GET", $methods[0]);
    }

    public function testGetHandler()
    {
        $this->assertEquals("WelcomeController->index", $this->routeRule->getHandler());
    }

    public function testGetRule()
    {
        $this->assertEquals("welcome/index/([a-z]+)/(\d+)", $this->routeRule->getRule());
    }

    public function testPattern()
    {
        $this->routeRule->setPattern('welcome/index/([a-z]+)/(\d+)');
        $this->assertEquals('welcome/index/([a-z]+)/(\d+)', $this->routeRule->getPattern());
    }

    public function testGetArgs()
    {
        $this->request = $this->createRequest("http://example.com/welcome/index/abc/10/10.50/1");
        $collection = (new RouteCollection($this->request, $this->config))
            ->build();
        $collection->route('GET', 'welcome/index/<name:str>/<id:int>/<price:float>/<active:bool>', 'WelcomeController->index');
        $collection->dispatch();
        $route   = $collection->getMatchedRoute();
        $handler = $collection->getMatchedHandler();
        $args = $route->getArgs();

        $this->assertEquals('abc', $args['name']);
        $this->assertInternalType('string', $args['name']);
        $this->assertEquals(10, $args['id']);
        $this->assertInternalType('integer', $args['id']);
        $this->assertEquals(10.50, $args['price']);
        $this->assertInternalType('float', $args['price']);
        $this->assertTrue($args['active']);
        $this->assertInternalType('bool', $args['active']);
        $this->assertEquals('WelcomeController->index', $handler);
    }

    public function testMiddleware()
    {
        $this->routeRule->middleware(new Tests\Middleware\Dummy)
            ->middleware(new Tests\Middleware\App);
        $stack = $this->routeRule->getMiddlewareStack();
        $this->assertInstanceOf('Tests\Middleware\Dummy', $stack[0]);
    }

    protected function createRequest($uri)
    {
        // Create a request
        $request = Zend\Diactoros\ServerRequestFactory::fromGlobals();
        $request = $request->withUri(new Zend\Diactoros\Uri($uri));
        return $request;
    }

}