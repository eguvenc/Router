
## Katmanlar

### Middleware eklemek

Router sınıfı dördüncü parametresi bir router kuralına,

```php
$collection->add('dummy',
    new Route(
        ['GET','POST'],
        '/dummy/(?<name>\w+)',
        'App\Controller\DefaultController:index',
        [App\Middleware\Dummy],
    )
);
```
pipe sınıfı ikinci parametresi ise bir pipe nesnesine http katmanı tayin eder.

```php
$pipe = new Pipe('test/','App\Middleware\Dummy','<str:name>.example.com',['http','https']);
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