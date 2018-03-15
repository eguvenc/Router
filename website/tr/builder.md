
## Kolleksiyon oluşturucu

Uygulamanızı daha anlaşılabilir kılmak amacı ile route verilerinizi bir dosya içerisinde tutmak isteyebilirsiniz. Kolleksiyon oluşturucu uygulamanızda tanımlı olan bir route dosyasını okuduktan sonra route kolleksiyon sınıfını oluşturmanızı sağlar.

### Yaml dosyaları

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

Kolleksiyon oluşturucu

```php
use Symfony\Component\Yaml\Yaml;

$data = Yaml::parseFile('/var/www/MyProject/App/routes.yaml');

$builder = new Builder($collection);
$collection = $builder->build($data);
```

* Eğer route verilerinin önbelleklenmesini istiyorsanız bu aşamayı build metodu aşamasından önce gerçekleştirebilirsiniz.

Url Çözümleme

```php
$router = new Router($collection);
if ($route = $router->matchRequest()) {

}
```

### Php dosyası

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

Kolleksiyon oluşturucu

```php
$data = require '/var/www/MyProject/App/routes.php';

$builder = new Builder($collection);
$collection = $builder->build($data);
```