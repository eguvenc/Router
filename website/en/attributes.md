
## Attributes

The attributes that can be assigned to a routing rule are as follows.

* method
* path
* handler
* middleware
* host
* scheme

The router class will not recognize any definitions other than those attributes. Definitions other than these attributes are possible with a special format.

### Setting an attribute

The `$` symbol should be used at the beginning of the attribute entered to be able to define the attribute.

```yaml
home: 
    path: /
    handler: App\Controller\DefaultController::index
    $variable:
        - test attribute
```

Getting value of the defined attribute.

```php
if ($route = $router->matchRequest()) {
    print_r($route->getAttribute('variable'); // array(test attribute)
}
```

Pipes

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

If the attribute is defined for a routing group, to get the value use `$pipe->getAttribute()` method.

```php
$router->matchRequest();
if ($pipe = $router->getMatchedPipe()) {
    echo $pipe->getAttribute('variable'); // test pipe attribute
}
```
