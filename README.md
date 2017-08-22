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
// $router->map('GET', 'welcome/index/(\d+)', 'Welcome/index/$1');
```

### Routing Map for Strict Types

```php
$router->map('GET', 'welcome/index/(?<id>\d+)/(?<month>\w+)', 'Welcome/index/$1/$2');
```

```php
$router->map('GET', 'welcome/index/(?<id>\d+)/(?<month>\w+)', function($request, $response) {
    $response->getBody()->write( print_r($request->getArgs(), true));
    return $response;
});
```

### Routing Map Integer Example

```php
$router->map('GET', '/users/(\w+)/(\d+)', '/Users/$1/$2');
$router->map('GET', '/users/(\w+)/(\d+)', function ($request, $response) {
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
                    function ($request, $response) {
                        
                        // var_dump($request->getArgs());
                        
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
                    function ($request, $response) {
                        
                        // var_dump($request->getArgs());
                        
                        $response->getBody()->write("yES !");

                        return $response;
                    }
                )->add('Guest')
                	->filter('contains', ['users/test/45'])->add('Guest');

                //->filter('notContains', ['users/teZ'])->add('Guest');;
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