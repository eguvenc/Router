
## Pipes

If you are designing an API or an expandable application, grouping the application routes by the pipe will improve your application's performance.

For example, when creating a data path called `users/`, a pipe combines the attributes of this group on the url.

```php
$pipe = new Pipe(
	'users/',
	[
		'middleware' => [App\Middleware\Dummy::class], 
		'host' => '<str:name>.example.com'
	]
);
$pipe->add(
	'test',
	new Route(
		[
			'method' => 'GET',
			'path' => '/',
			'handler' => 'App\Controller\DefaultController::index'
		]
	)
);
```

As long as the http request `users/` does not exist, the `preg_match` operation is not applied to the routes of this object, and performance will be improved because your application is fragmented.


```php
$collection = new RouteCollection($config);
$collection->setContext($context);

$collection->add(
	'home', 
	new Route(
		[
			'method' => 'GET',
			'path' => '/',
			'handler' => 'App\Controller\DefaultController::index'
		]
	)
);

$pipe = new Pipe(
	'users/example/', 
	[
		'middleware' => [App\Middleware\Dummy::class], 
		'host' => '<str:name>.example.com'
	]
);
$pipe->add(
	'dummy',
	new Route(
		[
			'method' => 'GET',
			'path' => '/<int:id>/<str:name>',
			'handler' => 'App\Controller\DefaultController::test'
		]
	)
);
$collection->addPipe($pipe);
```

Testing the values of an added route;

```php
$route = $collection->get('users/example/dummy');

echo $route->getHandler(); //  "App\Controller\DefaultController::test"
echo $route->getMethods()[0]; // GET
echo $route->getPattern(); //  "/users/example/(?\d+)/(?\w+)"
```

### Using pipes in .yaml files

If the last character of the highest level rule ends with a forward slash `/`, this rule is treated as a pipe.

```
users/:
    middleware: 
        - App\Middleware\Dummy
    dummy:
        path: /<int:id>/<str:name>
        handler: App\Controller\DefaultController::test
```