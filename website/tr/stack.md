
## Katmanlar

### Middleware eklemek

Router sınıfının dördüncü parametresi bir router kuralına http katmanı tayin eder.

```php
$collection->add('dummy',
    new Route(
        [
            'method' => ['GET','POST'],
            'path' => '/dummy/(?<name>\w+)',
            'handler' => 'App\Controller\DefaultController:index',
            'middleware' =>  [App\Middleware\Dummy::class],
        ]
    )
);
```

Pipe sınıfında ise ikinci parametreden http katmanı gönderilir.

```php
$pipe = new Pipe('test/',
    [
        'middleware' => App\Middleware\Dummy::class,
        'host' => '<str:name>.example.com',
        'scheme' => ['http','https']
    ]
);
```

Yaml içinde,

```
users/:
    middleware: 
        - App\Middleware\Dummy
    dummy:
        path: /<int:id>/<str:name>
        handler: App\Controller\DefaultController::test
        middleware: App\Middleware\Test
```

### Middleware dizisini almak

Router nesnesi `getStack` metodu route kolleksiyonuna atanan tüm middleware sınıflarına ulaşmanızı sağlar.

```php
if ($router->matchRequest()) {
    print_r($router->getStack()); // Array ( [0] => App\Middleware\Dummy)
}
```