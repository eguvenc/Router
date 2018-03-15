
## Translation

Your route rules require that the `TranslationType` class be configured to include multiple language support.

```php
$config = array(
    'types' => [
        new IntType('<int:id>'),
        new StrType('<str:name>'),
        new TranslationType('<locale:locale>'),
    ]
);
```

Optionally, you can use the second parameter to limit the languages to specific values.

```php
new TranslationType('<locale:locale>', '(?<%s>(en|de|es))');
```

Assume that you have a url constructed as below.

```
http://example.com/en/dummy/test
```

```php
$collection->add(
    'dummy',
    new Route(
        'GET',
        '/<locale:locale>/dummy/<str:name>',
        'App\Controller\DummyController::test'
    )
);
```

You can add the local argument of the matching route rule to the `$request` object. Once the local value has been copied, it should be deleted from the arguments as follows.

```php
if ($route = $router->matchRequest()) {
    $locale  = $route->getArgument('locale');
    $request = $request->withAttribute('locale', $locale);
    $route->removeArgument('locale');
}
```

Full example.

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

App\Controller\DummyController

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
