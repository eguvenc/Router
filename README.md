
## Router

Php standalone router package

### Enabling Restful Routing


```php
$router->restful(false);  // disable web routing style
```

### Http Get Routes


```php
$router->map('GET', '/users/(.*)',
     function ($request, $response, $args) use($router) {

         $response->getBody()->write('Users group');

       return $response;
});
```

### Rewriting All Routes

```php
$router->rewrite('GET', '(?:en|de|es|tr)|/(.*)', '$1');  // example.com/en/  (or) // example.com/en
```

### Routing Map

```php
$router->map('GET', '/', 'Welcome/index');
$router->map('GET', 'welcome', 'Welcome/index');
```

### Routing Map for Strict Types

```php
$router->map('GET', 'welcome/index/(?<id>\d+)/(?<month>\w+)', 'Welcome/index/$1/$2');
```

### Getting Mapped Arguments

Using <kbd>$args</kbd> variable you can reach mapped arguments.


```php
$router->map('GET', 'arguments/index/(?<id>\d+)/(?<month>\w+)', function($request, $response, $args) use($router) {
    $response->getBody()->write(print_r($args, true));
    return $response;
});
```

### Routing Map Integer Example

```php
$router->map('GET', '/users/(\w+)/(\d+)', '/Users/$1/$2');
$router->map('GET', '/users/(\w+)/(\d+)', function ($request, $response, $args) use($router) {
     var_dump($args);
});
```

### Route Groups & Middlewares

```php
$router->group(
    'users/',
    function () use ($router) {

        $router->group(
            'test/',
            function () use ($router) {
                
                $router->map(
                    'GET',
                    'users/test/(\w+)/(\d+).*',
                    function ($request, $response) use ($router) {
                        
                        $response->getBody()->write("yES !");

                        return $response;
                    }
                )->add('Guest');
            }
        );
    }
);
```


### Route Filters

```php
$router->group(
    'users/',
    function () use ($router) {

        $router->group(
            'test/',
            function () use ($router) {

                $router->map(
                    'GET',
                    'users/test/(\w+)/(\d+).*',
                    function ($request, $response) use ($router) {
                        
                        // var_dump($router->getArgs());
                        
                        $response->getBody()->write("yES !");

                        return $response;
                    }
                )->add('Guest')
                	->filter('contains', ['users/test/45'])->add('Guest');

                //->filter('notContains', ['users/teZ'])->add('Guest');
                //
                // ->ifContains(['login'])
                // ->ifNotContains(['login', 'payment'])
                // ->ifRegExp(['welcome/path/index'])
                // ->ifNotRegExp(['welcome/path/index'])
            }
        );
    }
);
```


### Dispatch


```php
$dispatcher = new Obullo\Router\Dispatcher($router->getPath());

$handler = null;
$dispatched = false;
$dispatcher->popGroup($request, $response, $router->getGroup());

foreach ($router->getRoutes() as $r) {
    if ($dispatcher->dispatch($r['pattern'])) {
        if (! in_array($request->getMethod(), (array)$r['method'])) {
            die("Method not allowed");
        }
        if (is_callable($r['handler'])) {
            $handler = $r['handler']($request, $response, $dispatcher->getArgs());
        }
    }
}

// If routes is not restful do web routing functionality.

if ($handler == null && $router->restful() == false) {
    $handler = $router->getPath();
}
if ($handler != null) {
    $dispatched = true;
}

var_dump($handler);
var_dump($dispatched);
```

### Middleware Dispatch


```php
$middleware = new Obullo\Router\Middleware('\App\Middleware\\');  // Optional
$dispatcher = new Obullo\Router\Dispatcher($router->getPath(), $middleware);

$handler = null;
$dispatched = false;
$dispatcher->popGroup($request, $response, $router->getGroup());
$dispatcher->popRoutes();

foreach ($router->getRoutes() as $r) {
    if ($dispatcher->dispatch($r['pattern'])) {
        if (! in_array($request->getMethod(), (array)$r['method'])) {
            $middleware->queue('NotAllowed', (array)$r['method']);
            continue; // stop
        }
        if (! empty($r['middlewares'])) {
            foreach ((array)$r['middlewares'] as $value) {
                $middleware->queue($value['name'], $value['params']);
            }
        }
        if (is_callable($r['handler'])) {
            $handler = $r['handler']($request, $response, $dispatcher->getArgs());
        }
    }
}

// If routes is not restful do web routing functionality.

if ($handler == null && $router->restful() == false) {
    $handler = $router->getPath();
}
if ($handler != null) {
    $dispatched = true;
}

var_dump($handler);
var_dump($dispatched);
```