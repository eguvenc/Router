
## Router

### MatchRequest

The MatchRequest method resolves an http request coming from the `Psr7 Request` object. Let's say we have a url structure like this:

```
http://example.com/<int:id>
http://example.com/<int:id>/<str:name>/
```

Definitions for optional parameter.

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

Route match

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

App\Controller\DummyController

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

> Using the Php ReflectionClass class, you can automatically call objects belonging to this class with the dependency injection method while resolving the controller class.


### Match

The match method allows matching to occur without the `Psr7 Request` object. Let's say we have a url structure like this:

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

Route match

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

### Url

The Url method generates secure addresses using the Generator class according to the route name entered.

```php
echo $router->url('dummy', ['locale' => 'en', 'name' => 'test']);
```

Output of above the example

```php
// '/en/dummy/test'
```