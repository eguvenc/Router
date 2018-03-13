
## Loaders

To make your application more understandable, you may want to keep the route collection in a file. The loaders create a route collection class by reading a route file defined in your application.

### Yaml file loader

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

$collection = new RouteCollection($config);
$collection->setContext($context);
```

Loader

```php
$loader = new YamlFileLoader;
$loader->load('/var/www/MyProject/App/routes.yaml');
$collection = $loader->build($collection);
```

Router

```php
$router = new Router($collection);
if ($route = $router->matchRequest()) {

}
```

### Php file loader

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

Loader

```php
$loader = new PhpFileLoader;
$loader->load('/var/www/MyProject/App/routes.php');
$collection = $loader->build($collection);
```

You only need to replace the loader part.