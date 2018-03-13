
## Route conditions

Conditional definitions are conditional mappings. The conditions can be defined for a route-by-pipe object or route.

### Host

The following route rule depends on the `test.example.com` host match.

```php
$collection->add('dummy',
    new Route(
        ['GET','POST'],
        '/dummy/(?<name>\w+)',
        'App\Controller\DefaultController:index',
        [],
        'test.example.com'
    )
);
```

The following route rule can be run depends on the `(?<name>\w+).example.com` host match.

```php
$collection->add('dummy',
    new Route(
        ['GET','POST'],
        '/dummy/(?<name>\w+)',
        'App\Controller\DefaultController:index',
        [],
        '<str:name>.example.com'
    )
);
```

If the host value is a regular expression, the `$router->getMatchedHosts()` method can be used to reach the host value.

```php
$router = new Router($collection);

if ($router->matchRequest()) {
    echo $router->getMatchedHosts()[0]; // test.example.com
    echo $router->getMatchedHosts()['name']; // test
}
```

An example for pipe.

```php
$pipe = new Pipe('test/','App\Middleware\Dummy','<str:name>.example.com',['http','https']);
```

### Scheme

A route binds the last parameter of the pipe rule to the uri scheme condition.

```php
$collection->add('dummy',
    new Route(
        ['GET','POST'],
        '/dummy/(?<name>\w+)',
        'App\Controller\DefaultController:index',
        [],
        'test.example.com'
        ['http', 'https']
    )
);
```

In .yaml file.

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

### Conditional loader

An installer solution that installs a different route file for each sub-domain name.

```php
$subdomain = strstr($context->getHost(), '.example.com', true); // admin

$loader->load('/var/www/MyProject/'.$subdomain.'_routes.yaml');
$collection = $loader->build($collection);
```