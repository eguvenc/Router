
# Obullo / Router

[![Build Status](https://travis-ci.org/obullo/Router.svg?branch=master)](https://travis-ci.org/obullo/Router)
[![License](https://img.shields.io/badge/License-BSD%203--Clause-blue.svg)](https://opensource.org/licenses/BSD-3-Clause)

> A standalone and secure Router package developed for Obullo-Pages inspired by <a href="https://docs.djangoproject.com/en/2.0/topics/http/urls/">Django Url Dispatcher</a>.


## Install

``` bash
$ composer require obullo/router
```

## Requirements

The following versions of PHP are supported by this version.

* 7.1
* 7.2
* 7.3
* 7.4

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
$config = [
    'types' => [
        new IntType('<int:id>'),
        new StrType('<str:name>'),
    ]
];
```

Psr7 Request

```php
$request = Laminas\Diactoros\ServerRequestFactory::fromGlobals();
$context = new RequestContext;
$context->fromRequest($request);
```

Route Collection

```php
$collection = new RouteCollection($config);
$collection->setContext($context);
$collection->add('home', new Route('GET', '/', 'Views/default.phtml'));
$collection->add('dummy', new Route('GET', '/dummy/index/<int:id>/<str:name>', 'Views/dummy.phtml'))->scheme(['http','https']);
$collection->add('test', new Route('GET', '/test/index', 'Views/test.phtml'))
    ->host('example.com');
    ->scheme('http');
    ->middleware(App\Middleware\Dummy::class);
```

Route Class

```php
use Obullo\Router\RouteInterface;

$route = $collection->get('dummy');

if ($route instanceof RouteInterface) {
    echo $route->getHandler(); //  "App\Controller\DummyController::index"
    echo $route->getMethods()[0]; // GET
    echo $route->getPattern(); //  "/dummy/index/(?\d+)/(?\w+)/"
    echo $route->getMiddlewares()[0]; // App\Middleware\Dummy::class
}
```

Dispatch

```php
$router = new Router($collection);

if ($route = $router->matchRequest()) {
    $handler = $route->getHandler(); // Views/default.phtml
    $response = include $handler;

    if ($response instanceof Psr\Http\Message\ResponseInterface) {
        echo $response->getBody();
    }
}
```

dummy.phtml

```php
// Views/dummy.phtml
// 
use Laminas\Diactoros\Response\HtmlResponse;

return new HtmlResponse('Im a dummy view');
```
