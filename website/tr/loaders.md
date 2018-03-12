
## Yükleyiciler

Uygulamanızı daha anlaşılabilir kılmak amacı ile route kolleksiyonunu  bir dosya içerisinde tutmak isteyebilirsiniz. Yükleyiciler uygulamanızda tanımlı olan bir route dosyasını okuyarak route kolleksiyon sınıfını oluştururlar.

### Yaml dosya yükleyicisi

Örnek bir .yaml dosyası

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

Psr7 İsteği

```php
$request = Zend\Diactoros\ServerRequestFactory::fromGlobals();
$context = new RequestContext;
$context->fromRequest($request);

$collection = new RouteCollection($config);
$collection->setContext($context);
```

Yükleyici

```php
$loader = new YamlFileLoader;
$loader->load('/var/www/MyProject/App/routes.yaml');
$collection = $loader->build($collection);
```

Url Çözümleme

```php
$router = new Router($collection);
if ($route = $router->matchRequest()) {

}
```

### Php dosya yükleyicisi

Örnek bir .php dosyası

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

Yükleyici

```php
$loader = new PhpFileLoader;
$loader->load('/var/www/MyProject/App/routes.php');
$collection = $loader->build($collection);
```

Bu kısımda sadece yükleyici değiştirmeniz yeterli olacaktır.