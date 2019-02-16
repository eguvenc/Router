
## Url üretici

Url üreticisi türler içerisinde `toUrl` metodunu kullanarak güvenli url adresleri yaratır.

```php
public function toUrl($value)
{
    return sprintf('%d', $value);
}
```

Bir örnek.

```php
$collection->add(
    'dummy/name',
    new Route(
        [
            'method' => 'GET'
            'path' => '/<locale:locale>/dummy/<str:name>',
            'handler' => 'App\Controller\DefaultController::dummy'
        ]
    )
);
```

Url generator sınıfına parametre gönderelim.

```php
echo (new Obullo\Router\Generator($collection))
	->generate('dummy/name', ['locale' => 'en', 'name' => 'test']);
```

Yukarıdaki örneğin çıktısı

```php
// '/en/dummy/test'
```

Başka bir örnek.

```php
$collection->add(
    'dummy/name/id',
    new Route(
        [
            'method' => 'GET',
            'path' => '/<str:name>/<int:id>',
            'handler' => 'App\Controller\DummyController::index'
        ]
    )
);
```

```php
echo (new Obullo\Router\Generator($collection))
	->generate('dummy/name/id', 'name' => 'test', 'id' => 5]);
```

Yukarıdaki örneğin çıktısı

```php
// '/test/5'
```