
## Pipe ile gruplama

Bir api tasarlıyorsanız yada uygulamanız genişleyebilir bir uygulama ise pipe ile uygulamayı parçalara ayırmak uygulamanızın performansını arttırır. 

Örneğin `users/` adında bir veri yolu oluşturursak bir pipe bu gruba ait nitelikleri url parçaları üzerinde birleştirir.

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

Http isteği `users/` gelmediği sürece bu nesneye ait route kümeleri için `preg_match` operasyonu uygulanmaz ve uygulamanız parçalara ayrıldığı için performanstan kazanılmış olur.

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

Eklenen bir route adına ait değerleri test etmek;

```php
$route = $collection->get('users/example/dummy');

echo $route->getHandler(); //  "App\Controller\DefaultController::test"
echo $route->getMethods()[0]; // GET
echo $route->getPattern(); //  "/users/example/(?\d+)/(?\w+)"
```

### Yaml içinde pipe

Eğer en yüksek seviyedeki bir kuralın son karakteri `"/"` ile bitiyorsa bu kural pipe olarak işlem görür.

```
users/:
    middleware: 
        - App\Middleware\Dummy
    dummy:
        path: /<int:id>/<str:name>
        handler: App\Controller\DefaultController::test
```