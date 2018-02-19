
# Obullo - Php7 Router

[![Build Status](https://travis-ci.org/obullo/Router.svg?branch=master)](https://travis-ci.org/obullo/Router)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/obullo/router.svg)](https://packagist.org/packages/obullo/router)

> Php7 router inspired from Django Url Dispatcher. Secure and Fast.

In addition, it supports the modern web router features like `Route groups`, `Route middleware` and `Restful Routing`.

## Install

Via Composer

``` bash
$ composer require obullo/router
```

## Getting Started

```php
require '../vendor/autoload.php';

use Obullo\Router\Route;
use Obullo\Router\Router;
use Obullo\Router\Pipe;
use Obullo\Router\RequestContext;
use Obullo\Router\RouteCollection;
use Obullo\Router\Loader\PhpFileLoader;
use Obullo\Router\Loader\YamlFileLoader;
use Obullo\Router\Types\{
    StrType,
    IntType,
    SlugType,
    AnyStrType,
    FourDigitYearType,
    TwoDigitMonthType,
    TwoDigitDayType
};
$request  = Zend\Diactoros\ServerRequestFactory::fromGlobals();

$start = microtime(true);

$configArray = array(
    'types' => [
        new IntType('<int:id>'),  // \d+
        new StrType('<str:name>'),     // \w+
        new StrType('<str:word>'),     // \w+
        new AnyStrType('<str:any>'),
        new AnyStrType('<str:any2>'),
        // new AnyPattern('<any:int>'),
        new IntType('<int:page>'),
        new SlugType('<slug:slug>'),
        new SlugType('<slug:slug_>', '(?<$name>[\w-_]+)$'), // slug with underscore
    ]
);
$config = new Zend\Config\Config($configArray);


$collection = new RouteCollection($config);

$pipe = new Pipe('users/example/', [App\Middleware\Dummy::class]);
$pipe->add('test', new Route('GET', '/test', 'App\Controller\DefaultController::test'));
$collection->add($pipe);

$collection->add(
    'home',
    new Route('GET', '/', 'App\Controller\DefaultController::index')
);
$collection->add(
    'welcome',
    new Route('GET', '/welcome/index/<int:id>/<str:name>', 'App\Controller\WelcomeController::index')
);

$context = new RequestContext;
$context->fromRequest($request);

$router = new Router(
    $context,
    $collection
);

$router->matchRequest();

if ($router->hasRouteMatch()) {
    
    $route   = $router->getMatchedRoute();
    $handler = $router->getMatchedHandler();

    $response = null;
    $args = array_merge(array('request' => $request), $route->getArgs());

    // Parse handlers

    if (is_callable($handler)) {
        $exp = explode('::', $handler);
        $class = new $exp[0];
        $method = $exp[1];
        $response = call_user_func_array(array($class, $method), $args);
    }

    // Emit response
    
    if ($response instanceof Psr\Http\Message\ResponseInterface) {
        echo '<h3>Response</h3>';
        echo '<hr size="1">';
        echo '<pre>';
        echo $response->getBody();
        echo '</pre>';
    }

    echo '<h3>Arguments</h3>';
    echo '<pre>';
    var_dump($route->getArgs());
    echo '</pre>';

    echo '<h3>Methods</h3>';
    echo '<pre>';
    var_dump($route->getMethods());
    echo '</pre>';

    echo '<h3>Pattern</h3>';
    echo '<pre>';
    echo htmlspecialchars($route->getPattern());
    echo '</pre>';
}
```

## Php Loader

## Yaml Loader


## Host configuration

[CONFIGURATION.md](CONFIGURATION.md)

## Requirements

The following versions of PHP are supported by this version.

* 5.6
* 7.0
* 7.1
* 7.2
* hhvm

## Testing

``` bash
$ vendor/bin/phpunit
```

## Languages

* [TR_CONFIGURATION.md](TR_CONFIGURATION.md)
* [TR_README.md](TR_README.md)



## Rewriting

If you want to change all route rules like above, use `rewrite` method at the top. So, you don't have to make changes in existing rules.

## Types


## Pipes

Route groups can be created with pipe function. Unless group name and the url segments match, group functions do not run.

## Middleware

> Optionally, Obullo router supports adding http layers to route rules with `obullo/middleware` composer package.

In the example below, a route rule is added a http layer named `Dummy`.
