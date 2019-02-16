
## Stack

### Adding middleware

The fourth parameter of the Router class assigns the middleware to a router rule.

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

In the Pipe class, the middleware is sent from the second parameter.

```php
$pipe = new Pipe('test/',
    [
        'middleware' => App\Middleware\Dummy::class,
        'host' => '<str:name>.example.com',
        'scheme' => ['http','https']
    ]
);
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