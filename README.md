
# Obullo Router

[![Build Status](https://travis-ci.org/obullo/Router.svg?branch=master)](https://travis-ci.org/obullo/Router)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/obullo/router.svg)](https://packagist.org/packages/obullo/router)

> Obullo router, hafif yükte yüksek performans hedeflenerek geliştirilmiş bağımsız bir php router paketidir.

Bununla birlikte `Route grupları`, `Route middleware`, `Restful Routing` gibi modern web router özelliklerini de destekler.

## Install

Via Composer

``` bash
$ composer require obullo/router
```

## Hello world

```php
require '../vendor/autoload.php';

$request = (Zend\Diactoros\ServerRequestFactory::fromGlobals())
            ->withUri(new Zend\Diactoros\Uri("http://example.com/hello"));

$response = new Zend\Diactoros\Response;

$router = new Router($request, $response);
$router->map('GET', 'hello.*', 'HelloWorldController->index');

$dispatcher = new Dispatcher($request, $response, $router);
$handler = $dispatcher->dispatch(
    new UrlMapper(
        $dispatcher,
        [
            'path' => $router->getPath(),
            'separator' => '->',
            'default.method' => 'index'
        ]
    )
);
var_dump($handler);  // (string) "HelloWorldController->index"
```

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


## Kurallar

```php
$router->map('GET', '/', 'WelcomeConroller->index');
$router->map('GET', 'welcome', 'WelcomeController->index');
```

Bu route kuralları `"/"` yada `"welcome"` istekleri geldiğinde `$handler` değişkeninden `"WelcomeController->index"` olarak çıktı elde edilmesini sağlar.

## Çözümleme

```php
$dispatcher = new Dispatcher($request, $response, $router);
$handler = $dispatcher->dispatch(
    new UrlMapper(
        $dispatcher,
        [
            'path' => $router->getPath(),
            'separator' => '->',
            'default.method' => 'index'
        ]
    )
);
if ($handler instanceof Zend\Diactoros\Response) {
    $response = $handler;
}
if ($handler instanceof UrlMapperInterface) {  // parse mapped variables
    $html = "<br /><br />";
    $html.= "<b>Class: </b>".$handler->getClass()."<br />";
    $html.= "<b>Method: </b>".$handler->getMethod()."<br />";
    $html.= "<b>First Argument: </b>".$handler->getArgs(0)."<br />";
    $response->getBody()->write($html);
}
echo $response->getBody();  // print body
```

## Http tabanlı kurallar

Eğer birden fazla http metodu tanımlamak isterseniz bu metotları bir dizi içerisinde tanımlamanız gerekir.

```php
$router->map(['GET','POST','PATCH'], 'users/.*',
     function ($request, $response, $mapper) use($router) {
         $response->getBody()->write('Welcome user !');
       return $response;
});
```

## Yeniden yazım

```php
$router->rewrite('GET', '(?:en|de|es|tr)|/(.*)', '$1');  // example.com/en/  (or) // example.com/en
```

Eğer tüm route kuralları yukarıdaki gibi değiştirilmek isteniyorsa `rewrite` metodu en tepede kullanılır. Böylece mevcut kurallarda değişiklik yapmak zorunda kalmazsınız.


## Kesin türler belirleme

```php
$router->map('GET', 'WelcomeController->index/(?<id>\d+)/(?<month>\w+)', 'WelcomeController->index');
```

`$mapper` nesnesi kullanılarak dışarıdan map edilen argümanlar elde edilmiş olur.

```php
$router->map('GET', 'arguments/index/(?<id>\d+)/(?<month>\w+)',
    function($request, $response, $mapper) use($router) {
        $response->getBody()->write(print_r($mapper->getArgs(), true));
        return $response;
    }
);
```

`$args` değişkeni aşağıdaki gibi çıktılanır.

```php
/*
Çıktı
array(2) {
  "id" => 155
  [0]=>
  string(3) "155"
  "month" => "October"
  [1]=>
  string(2) "October"
}
*/
```

Başka bir örnek yazım

```php
$router->map('GET', 'users/(\w+)/(\d+)', 'UserController->index');
$router->map('GET', 'users/(\w+)/(\d+)', function ($request, $response, $args) use($router) {
     var_dump($args);
});
```

## Kural grupları

Group fonksiyonu ile içe içe route grupları oluşturabilir. Grup adı ile url segmentleri eşleşmediği sürece grup fonksiyonları çalışmaz.

```php
$router->group(
    'group/',
    function () use ($router) {

        $router->group(
            'test/',
            function () use ($router) {

                $router->map(
                    'GET',
                    '(\w+)/(\d+).*',
                    function ($request, $response, $mapper) use ($router) {
                        $response->getBody()->write("It works !");
                        return $response;
                    }
                );
            }
        );
    }
);
```

## Middleware

> Obullo router opsiyonel olarak `obullo\middleware` paketi ile route kurallarına http katmanları ekleyebilmeyi destekler.

Aşağıdaki örnekte bir route kuralına `Dummy` adlı http katmanı ekleniyor.

```php
require '../vendor/autoload.php';

use Obullo\Router\Router;
use Obullo\Router\Dispatcher;
use Obullo\Router\UrlMapper;
use Obullo\Router\UrlMapperInterface;

use Obullo\Middleware\Queue;
use Obullo\Middleware\QueueInterface;

$request = (Zend\Diactoros\ServerRequestFactory::fromGlobals())
            ->withUri(new Zend\Diactoros\Uri("http://example.com/welcome"));
$response = new Zend\Diactoros\Response;

$queue = new Queue;
$queue->register('\App\Middleware\\');

$router = new Router($request, $response, $queue);
$router->map('GET', 'welcome', 'WelcomeController->index')->add('Dummy');

$dispatcher = new Dispatcher($request, $response, $router);
$handler = $dispatcher->dispatch(
    new UrlMapper(
        $dispatcher,
        [
            'path' => $router->getPath(),
            'separator' => '->',
            'default.method' => 'index'
        ]
    )
);
var_dump($handler);  // "object(UrlMapper)"
var_dump($queue->dequeue());    // ["callable"]=> object(App\Middleware\Dummy)#22 (0) {}
```

### Add metodu

Middleware bir route kuralına yada route grubuna `add` metodu kullanılarak eklenebilir.

```php
$router->group(
    'test/',
    function ($request, $response) use ($router) {

        $router->map(
            'GET',
            'dummy.*',
            function ($request, $response, $mapper) use ($router) {
                $response->getBody()->write("It works !");
                return $response;
            }
        );
    }

)->add('Dummy');
```

Add metodu ikinci parametresi opsiyonel olarak parametre gönderilmeyi destekler.

```php
$router->map('GET', 'welcome', 'WelcomeController->index')->add('Dummy', $params = array());
```

## Middleware filtreleri

> Http katmanları, http uri filtreleri kullanılarak belirli route kuralları yada gruplarına atanabilirler. Bu filtreler aşağıda sıralanmıştır.

### Contains filtresi

Aşağıdaki tanımlada route kuralı `test/foo/123` veya `test/foo/1234` segmentlerini içeriyorsa `Dummy` middleware sınıfı uygulamaya eklenmiş olur.

```php
$router->group(
    'example/',
    function () use ($router) {

        $router->group(
            'test/',
            function () use ($router) {

                $router->map(
                    'GET',
                    '(\w+)/(\d+).*',
                    function ($request, $response) use ($router) {
                        $response->getBody()->write("It works !");
                        return $response;
                    }

                )->filter('contains', ['test/foo/123', 'test/foo/1234'])->add('Dummy');
            }
        );
    }
);
```

### NotContains filtresi

Contains metodunun zıt yönlü filtresidir.

```php
$router->group(
    'example/',
    function () use ($router) {

        $router->group(
            'test/',
            function () use ($router) {

                $router->map(
                    'GET',
                    '(\w+)/(\d+).*',
                    function ($request, $response) use ($router) {
                        $response->getBody()->write("It works !");
                        return $response;
                    }

                )->filter('notContains', ['test/foo/888', 'test/foo/999'])->add('Dummy');
            }
        );
    }
);
```

### Regex filtresi

Aşağıdaki tanımlada route kuralı `.*?abc/(\d+)` düzenli ifadesini sağlayan segmentler için uygulamaya `Dummy` middleware sınıfını ekler.

```php
$router->group(
    'example/',
    function () use ($router) {

        $router->group(
            'test/',
            function () use ($router) {

                $router->map(
                    'GET',
                    '(\w+)/(\d+).*',
                    function ($request, $response) use ($router) {
                        $response->getBody()->write("It works !");
                        return $response;
                    }

                )->filter('regex', '.*?abc/(\d+)')->add('Dummy');
            }
        );

    }
);
```

### NotRegex filtresi

Regex metodunun zıt yönlü filtresidir.

```php
$router->group(
    'example/',
    function () use ($router) {

        $router->group(
            'test/',
            function () use ($router) {

                $router->map(
                    'GET',
                    '(\w+)/(.*)',
                    function ($request, $response) use ($router) {
                        $response->getBody()->write("It works !");
                        return $response;
                    }

                )->filter('notRegex', '.*?abc/(\d+)')->add('Dummy');
            }
        );

    }
);
```

## Örnekler

`/public` klasörü altında daha fazla örnek bulabilirsiniz.