
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

## Hello world

```php
require '../vendor/autoload.php';

$request = (Zend\Diactoros\ServerRequestFactory::fromGlobals())
            ->withUri(new Zend\Diactoros\Uri("http://example.com/hello"));

$response = new Zend\Diactoros\Response;

$router = new Router($request, $response);
$router->get('welcome.*', 'WelcomeController->index');

$mapper  = new UrlMapper($router);
$handler = $mapper->dispatch();

if (is_callable($handler)) {
    $handler = $handler($request, $response, $mapper);
}
if ($handler instanceof Zend\Diactoros\Response) {
    $response = $handler;
}
var_dump($handler);  // (string) "WelcomeController->index"
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


## Routing

### GET

```php
$router->get('/', 'WelcomeConroller->index');
$router->get('welcome', 'WelcomeController->index');
```
These route rules enables getting the output from the `$handler` variable as `"WelcomeController->index"` when receiving `"/"` or `"welcome"` requests.

### POST

```php
$router->post('foo/bar', 'PostConroller->index');
```

### PUT

```php
$router->put('foo/bar', 'PutConroller->index');
```

### PATCH

```php
$router->patch('foo/bar', 'PatchConroller->index');
```

### DELETE

```php
$router->delete('foo/bar', 'DeleteConroller->index');
```

### OPTIONS

```php
$router->options('foo/bar', 'OptionsConroller->index');
```

### Map

If you want to use more than one http methods, you need to define these methods within an array.

```php
$router->map(array('GET','POST','CUSTOM'), '/', function ($request, $response, $mapper) use($router) {
         $response->getBody()->write('Welcome user !');
       return $response;
});
```

## Dispatching url

```php
$mapper  = new UrlMapper($router);
$handler = $mapper->dispatch();

if (is_callable($handler)) {
    $handler = $handler($request, $response, $mapper);
}
if ($handler instanceof Zend\Diactoros\Response) {
    $response = $handler;
}
echo $response->getBody();  // print body

$mapper->getHandler(); // returns to handler
$mapper->getArgs(); // mapped arguments
$mapper->getMethods();  // current route methods
$mapper->getPattern();  // current route regex pattern
$mapper->getPathArray();  // exploded path of current route
```

## Rewriting

```php
$router->rewrite('GET', '(?:en|de|es|tr)|/(.*)', '$1');  // example.com/en/  (or) // example.com/en
```

If you want to change all route rules like above, use `rewrite` method at the top. So, you don't have to make changes in existing rules.

## Defining strict types

```php
$router->get('welcome/index/(?<id>\d+)/(?<month>\w+)', 'WelcomeController->index');
```

using `$mapper` object, you can get the arguments mapped from outside.

```php
$router->get('welcome/index/(?<id>\d+)/(?<month>\w+)',
    function($request, $response, $mapper) use($router) {
        $response->getBody()->write(print_r($mapper->getArgs(), true));
        return $response;
    }
);
```

`$args` are printed like below.

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

another example

```php
$router->map('GET', 'users/(\w+)/(\d+)', 'UserController->index');
$router->map('GET', 'users/(\w+)/(\d+)', function ($request, $response, $mapper) use($router) {
    $response->getBody()->write(print_r($mapper->getArgs(), true));
    return $response;
});
```

## Groups

Nested route groups can be created with Group function. Unless group name and the url segments match, group functions do not run.

```php
$router->group(
    'group/',
    function () use ($router) {

        $router->group(
            'test/',
            function () use ($router) {

                $router->get(
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

> Optionally, Obullo router supports adding http layers to route rules with `obullo/middleware` composer package.

In the example below, a route rule is added a http layer named `Dummy`.

```php
require '../vendor/autoload.php';

use Obullo\Router\Router;
use Obullo\Router\UrlMapper;

use Obullo\Middleware\Queue;
use Obullo\Middleware\QueueInterface;

$request = (Zend\Diactoros\ServerRequestFactory::fromGlobals())
            ->withUri(new Zend\Diactoros\Uri("http://example.com/welcome"));
$response = new Zend\Diactoros\Response;

$queue = new Queue;
$queue->register('\App\Middleware\\');

$router = new Router($request, $response, $queue);
$router->get('welcome', 'WelcomeController->index')->add('Dummy');

$mapper  = new UrlMapper($router);
$handler = $mapper->dispatch();

if (is_callable($handler)) {
    $handler = $handler($request, $response, $mapper);
}
if ($handler instanceof Zend\Diactoros\Response) {
    $response = $handler;
}
var_dump($handler);  // "WelcomeController->index"
var_dump($queue->dequeue());    // ["callable"]=> object(App\Middleware\Dummy)#22 (0) {}
```

### Add method

Middleware can be added to a route rule or route group using the method `add`.

```php
$router->group(
    'test/',
    function ($request, $response) use ($router) {

        $router->get(
            'dummy.*',
            function ($request, $response, $mapper) use ($router) {
                $response->getBody()->write("It works !");
                return $response;
            }
        );
    }

)->add('Dummy');
```

The second parameter of the add method optionally supports sending parameter.  

```php
$router->get('welcome', 'WelcomeController->index')->add('Dummy', array('foo' => 'bar'));
```

## Add filter

> Http layers can be assigned to certain route rules or route groups using http uri filters.

### Regex filter

In the definition below, the route rule adds the `Dummy` middleware class to application for the segments matching the regex `.*?abc/(\d+)`.

```php
use Obullo\Router\AddFilter\Regex;

$router->group(
    'example/',
    function () use ($router) {

        $router->group(
            'test/',
            function () use ($router) {

                $router->get(
                    '(\w+)/(\d+).*',
                    function ($request, $response) use ($router) {
                        $response->getBody()->write("It works !");
                        return $response;
                    }

                )->filter(new Regex('.*?abc/(\d+)'))->add('Dummy');
            }
        );

    }
);
```

## Examples

More examples can be found under the directory `/public`.