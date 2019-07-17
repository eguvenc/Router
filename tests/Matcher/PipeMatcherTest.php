<?php

use Obullo\Router\Pipe;
use Obullo\Router\Route;
use Obullo\Router\Matcher\PipeMatcher;

class PipeMatcherTest extends PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $pipe  = new Pipe('user/test/', [
            'handler' => 'App\Middleware\Dummy',
            'host' => '(?<name>\w+).example.com',
            'scheme' => ['http', 'https'],
        ]);
        $route = [
            'method' => 'GET',
            'path' => '/dummy/<str:name>/<int:id>',
            'handler' => 'App\Controller\DefaultController::index'
        ];
        $pipe->add('dummy', new Route($route));
        $this->matcher = new PipeMatcher($pipe);
    }

    public function testMatchPath()
    {
        $path = '/user/test/dummy/name/5/';
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
}