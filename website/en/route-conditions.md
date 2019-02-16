
## Route conditions

Conditional definitions are conditional mappings. The conditions can be defined for a route-by-pipe object or route.

### Host

The following route rule depends on the `test.example.com` host match.

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

The following route rule can be run depends on the `(?<name>\w+).example.com` host match.

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

A route binds the last parameter of the pipe rule to the uri scheme condition.

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

### Conditional builder

A route builder solution that load a different route file for each sub-domain name.

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