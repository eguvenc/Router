
## Router

### MatchRequest

MatchRequest metodu `Psr7 Request` nesnesi üzerinden gelen bir http isteğini çözümler. Aşağıdaki gibi bir url yapısına sahip olduğumuzu düşünelim.

```
http://example.com/<int:id>
http://example.com/<int:id>/<str:name>/
```

Opsiyonel parametre için tanımlar

```php
$collection->add(
    'dummy/id',
    new Route('GET', '/<int:id>', 'App\Controller\DummyController::index')
);
$collection->add(
    'dummy/id/name',
    new Route('GET', '/<int:id>/<str:name>', 'App\Controller\DummyController::index')
);
```
Eşleşme

```php
$router = new Router($collection);

if ($route = $router->matchRequest()) {

    $handler = $route->getHandler();
    $methods = $route->getMethods();
    if (! in_array($request->getMethod(), $methods)) {
        throw new Exception(
            sprintf('Method %s is not allowed.', $request->getMethod())
        );
    }
    $args = array_merge(array('request' => $request), $route->getArguments());
    // Parse handlers
    $response = null;
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
    public function index(Request $request, int $id, $name = '')
    {
        return new HtmlResponse(
            'Variables: #id:'.sprintf('%d', $id).' #name:'.sprintf('%s', $name)
        );
    }
}
```

> Php ReflectionClass sınıfını kullanarak kontrolör sınıfı çözümlenirken dependency injection yöntemi ile bu sınıfa ait nesneleri otomatik çağırabilirsiniz.


### Match

Match metodu `Psr7 Request` nesnesi olmadan eşleşme gerçekleştirmeye olanak sağlar. Aşağıdaki gibi bir url yapısına sahip olduğumuzu düşünelim.

```
http://example.com/<str:name>/<int:id>
```

```php
$collection = new RouteCollection($config);
$collection->setContext($context);
$collection->add(
    'dummy',
    new Route(
        'GET',
        '/dummy/<str:name>/<int:id>',
        'App\Controller\DefaultController::dummy'
    )
);
```

Eşleşme

```php
$router = new Router($collection);

if ($route = $router->match('/dummy/test/55','example.com','http')) {
    $args = $route->getArguments();

    var_dump($args['name']); // string "test"
    var_dump($args['id']); // integer 55

    echo $route->getMethods()[0]; // GET
    echo $route->getHandler(); // App\Controller\DefaultController::dummy
}
```