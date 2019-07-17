<?php

use Obullo\Router\Route;
use Obullo\Router\Matcher\RouteMatcher;

class RouteMatcherTest extends PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $route = new Route([
            'method' => 'GET',
            'path' => '/dummy/(?<name>\w+)/(?<id>\d+)',
            'handler' => 'App\Controller\DefaultController::index',
            'middleware' => ['App\Middleware\Dummy'],
            'host' => '(?<name>\w+).example.com',
            'scheme' => ['http','https']
        ]);
        $route->setName('dummy');
        $this->matcher = new RouteMatcher($route);
    }

    public function testMatchPath()
    {
        $path = '/dummy/name/5/';
        $this->assertTrue($this->matcher->matchPath($path));
    }

    public function testMatchHost()
    {
        $this->assertTrue($this->matcher->matchHost('test.example.com'));

        $hostMatches = $this->matcher->getHostMatches();

        $this->assertEquals('test.example.com', $hostMatches[0]);
        $this->assertEquals('test', $hostMatches[1]);
        $this->assertEquals('test', $hostMatches['name']);
    }

    public function testMatchScheme()
    {
        $this->assertTrue($this->matcher->matchScheme('http'));
        $this->assertTrue($this->matcher->matchScheme('https'));
    }

    public function testGetArguments()
    {
        $path = '/dummy/name/5/';
        $this->matcher->matchPath($path);
        $args = $this->matcher->getArguments();
        $this->assertEquals('name', $args['name']);
        $this->assertEquals('5', $args['id']);
    }
}