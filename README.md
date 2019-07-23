
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
$collection->add(new Route('GET', '/', 'Views/default.phtml'));
$collection->add(new Route('GET', '/dummy/index/<int:id>/<str:name>', 'Views/dummy.phtml')
    ->middleware(App\Middleware\Dummy::class);
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

    $response = include $handler;

    if ($response instanceof Psr\Http\Message\ResponseInterface) {
        echo $response->getBody();  // Views/dummy.phtml
    }
}
```

dummy.phtml

```php
// Views/dummy.phtml
// 
use Zend\Diactoros\Response\HtmlResponse;

return new HtmlResponse('Im a dummy view');
```

### YAML example

Yaml basic

```yaml
/:
    handler: Views/default.phtml

/<locale:locale>/dummy/<str:name>:
     handler: Views/dummy.phtml
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