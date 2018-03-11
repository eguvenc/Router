
# Php7 Router

[![Build Status](https://travis-ci.org/obullo/Router.svg?branch=master)](https://travis-ci.org/obullo/Router)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/obullo/router.svg)](https://packagist.org/packages/obullo/router)

> Obullo Php7 router Django Url Dispatcher kütühanesinden ilham alınarak geliştirilmiştir ve anlaşılabilir olmayı hedefler.

## Yükleme

Via Composer

``` bash
$ composer require obullo/router
```

## Host configurasyonu

[CONFIGURATION.md](CONFIGURATION.md)

## Gereksinimler

Bu versiyon aşağıdaki PHP sürümleri tarafından destekleniyor.

* 7.0
* 7.1
* 7.2

## Test

``` bash
$ vendor/bin/phpunit
```

## Diller

* [TR_CONFIGURATION.md](TR_CONFIGURATION.md)
* [TR_README.md](TR_README.md)


## Hızlı Başlangıç

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

Route Kolleksiyonu

```php
$collection = new RouteCollection($config);
$collection->setContext($context);
$collection->add('home', new Route('GET', '/', 'App\Controller\DefaultController::index'));
$collection->add(
    'dummy',
    new Route('GET', '/dummy/index/<int:id>/<str:name>', 'App\Controller\DummyController::index')
);
$route = $collection->get('dummy');

echo $route->getHandler(); //  "App\Controller\DummyController::index"
echo $route->getMethods()[0]; // GET
echo $route->getPattern(); //  "/dummy/index/(?\d+)/(?\w+)"
```

Çözümleme

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

App\Controller\DummyController sınıfı

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

## Tür Konfigürasyonu

Önceden belirlenen tür tanımları route kuralları içerisindeki argümanların daha esnek bir biçimde yönetilmesini sağlar ve güvenliği arttırır.

```php
$configArray = array(
    'types' => [
        new IntType('<int:id>'),
        new StrType('<str:name>'),
        new StrType('<str:word>'),
        new AnyType('<any:any>'),
        new BoolType('<bool:status>'),
        new IntType('<int:page>'),
        new SlugType('<slug:slug>'),
        new SlugType('<slug:slug_>', '(?<%s>[\w-_]+)'), // slug with underscore
    ]
);
$config = new Zend\Config\Config($configArray);
```

Aşağıda Int türüne ait bir örnek gösteriliyor.

```
class IntType extends Type
{
    protected $regex = '(?<%s>\d+)';

    /**
     * Php format
     * 
     * @param  number $value 
     * @return int
     */
    public function toPhp($value)
    {
        return (int)$value;
    }

    /**
     * Url format
     * 
     * @param mixed $value
     * @return string
     */
    public function toUrl($value)
    {
        return sprintf('%d', $value);
    }
}
```

* `regex` değişkeni içerisindeki `%s` değeri `<int:id>` gibi bir türe ilişkin ismin `(?<id>\d+)` ifadesine dönüştürülmesini sağlar.
* `toPhp` metodu gelen argüman türünü php içerisinde kullanılmadan önce belirlenen türe dönüştürür.
* `toUrl` metodu UrlGenerator sınıfını kullanarak güvenli url linkleri oluşturmanızı sağlar.
* Abstract Type sınıfına genişleyerek kendi türlerinizi de oluşturabilirsiniz.


Tanımlı türü bozmanda ön tanımlı bir regex değerini değiştirmek için construct metodu ikinci parametresini kullanabilirsiniz.

```php
new SlugType('<slug:slug>');
new SlugType('<slug:slug_>', '(?<%s>[\w-_]+)');  // slug with underscore
```

Klonlanan ikinci türe ait isim mutlaka değiştirilmelidir. Yukarıdaki örnekte slug türü klonlanarak alt çizgi desteği eklendi. `<slug:slug_>`


## Yükleyiciler

### Yaml dosya yükleyicisi

### Php dosya yükleyicisi



### Route Kolleksiyonu

```php

```

## Pipe ile gruplama


### Yaml örneği


## Url Çözümleme

### Match


### MatchRequest


## Koşullu tanımlamalar

### Host

### Scheme


## Stack

### Middleware eklemek

### Middleware dizisini almak


## Url Generator


## Yerelleştirme