
## Nitelikler

Bir yönlendirme kuralına atanabilecek nitelikler aşağıdaki gibidir.

* method
* path
* handler
* middleware
* host
* scheme

Router sınıfı bu nitelikler dışındaki tanımlamaları tanımayacaktır. Bu nitelikler dışındaki tanımlamalar özel bir yazım biçimi ile mümkündür.

### Nitelik tanımlamak

Nitelik tanımlamaları yapabilmek için girilen niteliğin başında `$` sembolü kullanılmalıdır.

```yaml
home: 
    path: /
    handler: App\Controller\DefaultController::index
    $variable:
        - test attribute
```

Tanımlanan niteliğin değeri `$route->getAttribute()` metodu ile alınır.

```php
if ($route = $router->matchRequest()) {
    print_r($route->getAttribute('variable'); // array(test attribute)
}
```

Gruplar

```yaml
user/:
    $variable: test pipe attribute
    middleware: 
        - App\Middleware\Auth
    dummy:
        path: /dummy/<str:name>
        handler: App\Controller\UserController::dummy
    lucky:
        path: /lucky/<str:name>/<slug:slug>
        handler: App\Controller\DefaultController::lucky
```

Eğer nitelik bir yönlendirme gurubu için tanımlanmışsa niteliğin değeri `$pipe->getAttribute()` metodu ile alınır.


```php
$router->matchRequest();
if ($pipe = $router->getMatchedPipe()) {
    echo $pipe->getAttribute('variable'); // test pipe attribute
}
```
