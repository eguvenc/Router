
# Obullo / Router

[![Build Status](https://travis-ci.org/obullo/Router.svg?branch=master)](https://travis-ci.org/obullo/Router)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/obullo/router.svg)](https://packagist.org/packages/obullo/router)

> Obullo router is a standalone route package inspired by the <a href="https://docs.djangoproject.com/en/2.0/topics/http/urls/">Django Url Dispatcher</a>.


## Install

``` bash
$ composer require obullo/router
```

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
$config = array(
    'types' => [
        new IntType('<int:id>'),
        new StrType('<str:name>'),
    ]
);
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
$collection->add('/', new Route(['handler' => 'App\Controller\DefaultController::index'));
$collection->add(
    '/dummy/index/<int:id>/<str:name>',
    new Route(
        [
            'method' => 'GET',
            'handler' => 'App\Controller\DummyController::index'
            'middleware' => ['App\Middleware\Dummy::class']
        ]
    )
);
```

Route Class

```php
$route = $collection->get('/dummy/index/<int:id>/<str:name>');

echo $route->getHandler(); //  "App\Controller\DummyController::index"
echo $route->getMethods()[0]; // GET
echo $route->getPattern(); //  "/dummy/index/(?\d+)/(?\w+)/"
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

### YAML example

Yaml basic

```yaml
/:
    handler: App\Controller\DefaultController::index

/<locale:locale>/dummy/<str:name>:
     handler: App\Controller\DefaultController::dummy
     middleware: App\Middleware\Dummy
```

Parsing yaml

```php
use Obullo\Router\Route;
use Obullo\Router\RequestContext;
use Obullo\Router\RouteCollection;
use Obullo\Router\Router;
use Obullo\Router\Builder;
use Obullo\Router\Generator;
use Obullo\Router\Types\{
    StrType,
    IntType,
    SlugType,
    TranslationType
};
$config = array(
    'patterns' => [
        new IntType('<int:id>'),
        new StrType('<str:name>'),
        new SlugType('<slug:slug>'),
        new TranslationType('<locale:locale>'),
    ]
);
$request = Zend\Diactoros\ServerRequestFactory::fromGlobals();
$context = new RequestContext;
$context->fromRequest($request);

$collection = new RouteCollection($config);
$collection->setContext($context);

use Symfony\Component\Yaml\Yaml;

$builder = new Builder($collection);
$collection = $builder->build(Yaml::parseFile('/var/www/myproject/App/routes.yaml'));

if ($route = $router->matchRequest()) {
    echo $handler = $route->getHandler();  // App\Controller\DefaultController::index
    $methods = $route->getMethods();
}
```

## Documentation

Documents are available at <a href="http://obullo.com/">http://obullo.com/</a>