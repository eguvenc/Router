
## Pipe ile gruplama

Bir api tasarlıyorsanız yada uygulamanız genişleyebilir bir uygulama ise pipe ile uygulamayı parçalara ayırmak uygulamanızın performansını arttırır. 

Örneğin `users/` adında bir veri yolu oluşturursak bir pipe bu gruba ait nitelikleri url parçaları üzerinde birleştirir.

```php
$pipe = new Pipe('users/', [App\Middleware\Dummy::class], '<str:name>.router');
$pipe->add('test', new Route('GET', '/test', 'App\Controller\DefaultController::test'));
```

Http isteği `users/` gelmediği sürece bu nesneye ait route kümeleri için `preg_match` operasyonu uygulanmaz ve uygulamanız parçalara ayrıldığı için performanstan kazanılmış olur.

```php
$collection = new RouteCollection($config);
$collection->setContext($context);

$collection->add('home', new Route('GET', '/', 'App\Controller\DefaultController::index'));

$pipe = new Pipe('users/example/', [App\Middleware\Dummy::class], '<str:name>.router');
$pipe->add('dummy', new Route('GET', '/<int:id>/<str:name', 'App\Controller\DefaultController::test'));
$collection->addPipe($pipe);
```

Eklenen bir route adına ait değerleri test etmek;

```php
$route = $collection->get('users/example/dummy');

echo $route->getHandler(); //  "App\Controller\DefaultController::test"
echo $route->getMethods()[0]; // GET
echo $route->getPattern(); //  "/users/example/(?\d+)/(?\w+)"
```

### Yaml içinde pipe

Eğer en yüksek seviyedeki bir kuralın son karakteri forward slash `"/"` ile bitiyorsa bu kural pipe olarak işlem görür.  Örnek `"users/"`.

```
users/:
    middleware: 
        - App\Middleware\Dummy
    dummy:
        path: /<int:id>/<str:name>
        handler: App\Controller\DefaultController::test
```