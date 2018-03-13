
## Stack

### Adding middleware

The fourth parameter of the Router class assigns the middleware to a router rule.

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

In the Pipe class, the middleware is sent from the second parameter.

```php
$pipe = new Pipe('test/','App\Middleware\Dummy','<str:name>.example.com',['http','https']);
```

In .yaml file.

```
users/:
    middleware: 
        - App\Middleware\Dummy
    dummy:
        path: /<int:id>/<str:name>
        handler: App\Controller\DefaultController::test
        middleware: App\Middleware\Test
```

### Getting middlewares

The Router object `getStack` method allows you to reach all middleware classes assigned to the route collection.

```php
if ($router->matchRequest()) {
    print_r($router->getStack()); // Array ( [0] => App\Middleware\Dummy)
}
```