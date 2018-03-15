
# Obullo / Router

[![Build Status](https://travis-ci.org/obullo/Router.svg?branch=master)](https://travis-ci.org/obullo/Router)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/obullo/router.svg)](https://packagist.org/packages/obullo/router)

> Obullo router <a href="https://docs.djangoproject.com/en/2.0/topics/http/urls/">Django Url Dispatcher</a> kütüphanesinden ilham alınarak geliştirilmiş bağımsız bir route paketidir.

## Yükleme

``` bash
$ composer require obullo/router
```

## Host konfigürasyonu

[Configuration.md](/tr/configuration.md)

## Gereksinimler

Bu versiyon aşağıdaki PHP sürümleri tarafından destekleniyor.

* 7.0
* 7.1
* 7.2

## Test

``` bash
$ vendor/bin/phpunit
```

## Hızlı başlangıç

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

Psr7 İsteği

```php
$request = Zend\Diactoros\ServerRequestFactory::fromGlobals();
$context = new RequestContext;
$context->fromRequest($request);
```

Route Kolleksiyonu

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

Route Sınıfı

```php
$route = $collection->get('dummy');

echo $route->getHandler(); //  "App\Controller\DummyController::index"
echo $route->getMethods()[0]; // GET
echo $route->getPattern(); //  "/dummy/index/(?\d+)/(?\w+)"
echo $route->getStack()[0]; // App\Middleware\Dummy::class
```

Url Çözümleme

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

App\Controller\DummyController örneği

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

## Türler

[Types.md](/tr/types.md)

## Kolleksiyon oluşturucu

[Builder.md](/tr/builder.md)

## Pipe ile gruplama

[Pipes.md](/tr/pipes.md)

## Url çözümleme

[Router.md](/tr/router.md)

## Koşullu tanımlamalar

[RouteConditions.md](/tr/route-conditions.md)

## Katmanlar

[Stack.md](/tr/stack.md)

## Yerelleştirme

[Translation.md](/tr/translation.md)

## Url üretici

[Generator.md](/tr/generator.md)

## Performans

[Performance.md](/tr/performance.md)