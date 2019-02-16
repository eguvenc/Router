
## Yerelleştirme

Route kurallarınıza çoklu dil desteği eklemek için `TranslationType` sınıfının konfigüre edilmesi gerekir. 

```php
$config = array(
    'types' => [
        new IntType('<int:id>'),
        new StrType('<str:name>'),
        new TranslationType('<locale:locale>'),
    ]
);
```

Opsiyonel olarak, dilleri belirli değerlere sınırlamak için ikinci parametreyi kullanabilirsiniz.

```php
new TranslationType('<locale:locale>', '(?<%s>(en|de|es))');
```

Aşağıdaki gibi bir url yapımız olduğunu varsayalım.

```
http://example.com/en/dummy/test
```

```php
$collection->add(
    'dummy',
    new Route(
        [
            'method' => 'GET',
            'path' => '/<locale:locale>/dummy/<str:name>',
            'handler' => 'App\Controller\DummyController::test'
        ]
    )
);
```

Eşleşen route kuralına ait yerel argümanı $request nesnesine ekleyebilirsiniz. Yerel değer kopyalandıktan sonra argümanlardan aşağıdaki gibi silinmelidir.

```php
if ($route = $router->matchRequest()) {
    $locale  = $route->getArgument('locale');
    $request = $request->withAttribute('locale', $locale);
    $route->removeArgument('locale');
}
```

Tam örnek 

```php
if ($route = $router->matchRequest()) {

    $response = null;
    $locale  = $route->getArgument('locale');
    $request = $request->withAttribute('locale', $locale);
    $route->removeArgument('locale');
    
    $args = array_merge(array('request' => $request), $route->getArguments());
    $handler = $route->getHandler();

    // Parse handlers
    if (is_callable($handler)) {
        $exp = explode('::', $handler);
        $class = new $exp[0];
        $method = $exp[1];
        $response = call_user_func_array(array($class, $method), $args);
    }
    // Emit response
    if ($response instanceof Psr\Http\Message\ResponseInterface) {
        echo $response->getBody();
    }
}
```

App\Controller\DummyController sınıfı

```php
namespace App\Controller;

use Zend\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\RequestInterface as Request;

class DummyController
{
    public function test(Request $request)
    {
        $locale = $request->getAttribute('locale');

        return new HtmlResponse('DummyController::test #locale:'.sprintf('%02s', $locale));
    }
}
```
