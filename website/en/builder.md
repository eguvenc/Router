
## Builder

To make your application more understandable, you may want to keep the route data in a file. Once you have read a route file defined in your application then builder will be able to create the route collection class.

### Yaml file

An example .yaml file.

```
home: 
    path: /
    handler: App\Controller\DefaultController::index
admin/:
    host: example.com
    scheme: http
    middleware: 
        - App\Middleware\Dummy
    dummy:
        path: /dummy/<str:name>/<int:id>
        handler: App\Controller\DefaultController::dummy
```

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

$collection = new RouteCollection($config);
$collection->setContext($context);
```

Builder

```php
use Symfony\Component\Yaml\Yaml;

$data = Yaml::parseFile('/var/www/MyProject/App/routes.yaml');

$builder = new Builder($collection);
$collection = $builder->build($data);
```

* If you want to cache route data, you can do this step before the build method.

Router

```php
$router = new Router($collection);
if ($route = $router->matchRequest()) {

}
```

### Php file

An example .php file.

```
return [
    'home' => [
        'path'   => '/',
        'handler'=> 'App\Controller\DefaultController::index',
    ],
    'admin/' => [
        'host' => 'example.com',
        'scheme' => 'http',
        'middleware' => [App\Middleware\Auth::class],
        'dummy' => [
            'path'   => '/dummy/<str:name>/<int:id>',
            'handler'=> 'App\Controller\DefaultController::dummy',
        ]
    ]
];
```

Builder

```php
$data = require '/var/www/MyProject/App/routes.php';

$builder = new Builder($collection);
$collection = $builder->build($data);
```