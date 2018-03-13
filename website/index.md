
# Obullo / Router

[![Build Status](https://travis-ci.org/obullo/Router.svg?branch=master)](https://travis-ci.org/obullo/Router)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/obullo/router.svg)](https://packagist.org/packages/obullo/router)

> Obullo router is a standalone route package inspired by the <a href="https://docs.djangoproject.com/en/2.0/topics/http/urls/">Django Url Dispatcher</a> package.


## Install

``` bash
$ composer require obullo/router
```

## Host configuration

[Configuration.md](/en/configuration.md)

## Requirements

The following versions of PHP are supported by this version.

* 7.0
* 7.1
* 7.2

## Testing

``` bash
$ vendor/bin/phpunit
```

## Quick start

```php
require '../vendor/autoload.php';

use Obullo\Router\Route;
use Obullo\Router\RequestContext;
use Obullo\Router\RouteCollection;
use Obullo\Router\Router;
use Obullo\Router\Types\{
    StrType,
    IntType
};
$configArray = array(
    'types' => [
        new IntType('<int:id>'),
        new StrType('<str:name>'),
    ]
);
$config = new Zend\Config\Config($configArray);
```

Psr7 Request

```php
$request = Zend\Diactoros\ServerRequestFactory::fromGlobals();
$context = new RequestContext;
$context->fromRequest($request);
```

Route Collection

```php
$collection = new RouteCollection($config);
$collection->setContext($context);
$collection->add('home', new Route('GET', '/', 'App\Controller\DefaultController::index'));
$collection->add(
    'dummy',
    new Route(
        'GET',
        '/dummy/index/<int:id>/<str:name>',
        'App\Controller\DummyController::index'
        ['App\Middleware\Dummy::class']
    )
);
```

Route Class

```php
$route = $collection->get('dummy');

echo $route->getHandler(); //  "App\Controller\DummyController::index"
echo $route->getMethods()[0]; // GET
echo $route->getPattern(); //  "/dummy/index/(?\d+)/(?\w+)"
echo $route->getStack()[0]; // App\Middleware\Dummy::class
```

Dispatching

```php
$router = new Router($collection);

if ($route = $router->matchRequest()) {

    $handler = $route->getHandler();
    $args = array_merge(array('request' => $request), $route->getArguments());
    $response = null;
    if (is_callable($handler)) {
        $exp = explode('::', $handler);
        $class = new $exp[0];
        $method = $exp[1];
        $response = call_user_func_array(array($class, $method), $args);
    }
    if ($response instanceof Psr\Http\Message\ResponseInterface) {
        echo $response->getBody();  // DummyController::index
    }
}
```

App\Controller\DummyController

```php
namespace App\Controller;

use Zend\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\RequestInterface as Request;

class DummyController
{
    public function index(Request $request)
    {
        return new HtmlResponse('DummyController::index');
    }
}
```

## Types

[Types.md](/en/types.md)

## Loaders

[Loaders.md](/en/loaders.md)

## Pipes

[Pipes.md](/en/pipes.md)

## Router

[Router.md](/en/router.md)

## Route Conditions

[RouteConditions.md](/en/route-conditions.md)

## Stack

[Stack.md](/en/stack.md)

## Translation

[Translation.md](/en/translation.md)

## Generator

[Generator.md](/en/generator.md)

## Performance

[Performance.md](/en/performance.md)