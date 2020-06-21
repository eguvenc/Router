
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

use Obullo\Router\Pattern;
use Obullo\Router\Route;
use Obullo\Router\RequestContext;
use Obullo\Router\RouteCollection;
use Obullo\Router\Router;
use Obullo\Router\Types\{
    StrType,
    IntType
};
$pattern = new Pattern([
    new IntType('<int:id>'),
    new StrType('<str:name>'),
]);
```

Psr7 Request

```php
$request = Zend\Diactoros\ServerRequestFactory::fromGlobals();
$context = new RequestContext;
$context->fromRequest($request);
```

Route Collection

```php
$collection = new RouteCollection($pattern);
$collection->setContext($context);
$collection->add(new Route('GET', '/', 'Views/default.phtml'));
$collection->add(new Route('GET', '/dummy/index/<int:id>/<str:name>', 'Views/dummy.phtml'))->scheme(['http','https']);
$collection->add(new Route('GET', '/test/index', 'Views/test.phtml'))
    ->host('example.com');
    ->scheme('http');
    ->middleware(App\Middleware\Dummy::class);
```

Route Class

```php
$route = $collection->get('/dummy/index/<int:id>/<str:name>');

echo $route->getHandler(); //  "App\Controller\DummyController::index"
echo $route->getMethods()[0]; // GET
echo $route->getPattern(); //  "/dummy/index/(?\d+)/(?\w+)/"
echo $route->getMiddlewares()[0]; // App\Middleware\Dummy::class
```

Dispatching

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
use Zend\Diactoros\Response\HtmlResponse;

return new HtmlResponse('Im a dummy view');
```

### YAML example

Yaml basic

```yaml
## routes.yaml

/:
    handler: Views/default.phtml

/<locale:locale>/dummy/<str:name>:
     handler: Views/dummy.phtml
     middleware: App\Middleware\Dummy
```

Parsing yaml

```php
require '../vendor/autoload.php';

use Obullo\Router\Pattern;
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
$pattern = new Pattern;
$pattern->add(new IntType('<int:id>'));
$pattern->add(new StrType('<str:name>'));
$pattern->add(new SlugType('<slug:slug>'));
$pattern->add(new TranslationType('<locale:locale>'));

$request = Zend\Diactoros\ServerRequestFactory::fromGlobals();
$context = new RequestContext;
$context->fromRequest($request);

$collection = new RouteCollection($pattern);
$collection->setContext($context);

use Symfony\Component\Yaml\Yaml;

$builder = new Builder($collection);
$collection = $builder->build(Yaml::parseFile('routes.yaml'));

$router = new Router($collection);

if ($route = $router->matchRequest()) {
    echo $handler = $route->getHandler();  // Views/default.phtml
    $methods = $route->getMethods();
}
```