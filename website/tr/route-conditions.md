
## Koşullu tanımlamalar

Koşullu tanımlamalar eşleşmeyi koşula bağlarlar. Koşullar bir route yada pipe nesnesi için tanımlanabilir.

### Host

Aşağıdaki route kuralının çalışabilmesi `test.example.com` host eşleşmesine bağlıdır.

```php
$collection->add('dummy',
    new Route(
        [
            'method' => ['GET','POST'],
            'path' => '/dummy/(?<name>\w+)',
            'handler' => 'App\Controller\DefaultController:index',
            'host' => 'test.example.com'
        ]
    )
);
```

Aşağıdaki route kuralının çalışabilmesi  `(?<name>\w+).example.com` host eşleşmesine bağlıdır.

```php
$collection->add('dummy',
    new Route(
        [
            'method' => ['GET','POST'],
            'path' => '/dummy/(?<name>\w+)',
            'handler' => 'App\Controller\DefaultController:index',
            'host' => '<str:name>.example.com'
        ]
    )
);
```

Host değeri eğer bir düzenli ifade ise `$router->getMatchedHosts()` metodu ile host eşleşmeleri alınabilir.

```php
$router = new Router($collection);

if ($router->matchRequest()) {
    echo $router->getMatchedHosts()[0]; // test.example.com
    echo $router->getMatchedHosts()['name']; // test
}
```

Pipe için bir örnek

```php
$pipe = new Pipe(
    'test/',
    [
        'middleware' => App\Middleware\Dummy::class,
        'host' => '<str:name>.example.com',
        'scheme' => ['https']
    ]
);
```

### Scheme

Bir route yada pipe kuralının son parametresi eşleşmeyi uri scheme koşuluna bağlar.

```php
$collection->add('dummy',
    new Route(
        [
            'method' => ['GET','POST'],
            'path' => '/dummy/(?<name>\w+)',
            'handler' => 'App\Controller\DefaultController:index',
            'host' => 'test.example.com'
            'scheme' => ['http', 'https']
        ]
    )
);
```

Yaml içinde,

```
admin/:
    host: admin.example.com
    scheme:
        - http
        - https
    middleware:
        - App\Middleware\Auth
    dummy:
        path: /dummy/<str:name>
        handler: App\Controller\UserController::dummy
```

### Koşullu yükleyici

Herbir alt domain adına ait farklı route dosyası yükleyen bir yükleyici.

```php
use Symfony\Component\Yaml\Yaml;

$request = Zend\Diactoros\ServerRequestFactory::fromGlobals();
$context = new RequestContext;
$context->fromRequest($request);

$collection = new RouteCollection($config);
$collection->setContext($context);

$subdomain = strstr($context->getHost(), '.example.com', true); // admin.example.com
$data = Yaml::parseFile('/var/www/MyProject/'.$subdomain.'/routes.yaml');

$builder = new Builder($collection);
$collection = $builder->build($data);
```