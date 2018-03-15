
## Generator class

The URL generator class creates secure url addresses within the types using the `toUrl` method.

```php
public function toUrl($value)
{
    return sprintf('%d', $value);
}
```

An example.

```php
$collection->add(
    'dummy/name',
    new Route(
        'GET',
        '/<locale:locale>/dummy/<str:name>',
        'App\Controller\DefaultController::dummy'
    )
);
```

Let's send parameters to generator class.

```php
echo (new Obullo\Router\Generator($collection))
	->generate('dummy/name', ['locale' => 'en', 'name' => 'test']);
```

Output of above the example.

```php
// '/en/dummy/test'
```

An another example.

```php
$collection->add(
    'dummy/name/id',
    new Route('GET', '/<str:name>/<int:id>', 'App\Controller\DummyController::index')
);
```

```php
echo (new Obullo\Router\Generator($collection))
	->generate('dummy/name/id', 'name' => 'test', 'id' => 5]);
```

Output of above the example.

```php
// '/test/5'
```