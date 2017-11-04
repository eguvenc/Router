<?php

include 'header.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../vendor/autoload.php';

use Obullo\Router\Router;

$request  = Zend\Diactoros\ServerRequestFactory::fromGlobals();
$response = new Zend\Diactoros\Response;

$router = new Router($request->getUri()->getPath(), $request->getMethod(), $request->getServerParams());
$router->restful(true);

//--------------------------------------------------------------------
// Example routes
//--------------------------------------------------------------------

$router->rewrite('GET', '(?:en|de|es|tr)|/(.*)', '$1');  // example.com/en/  (or) // example.com/en

$router->map('GET', '/', 'Welcome/index');
$router->map('GET', 'welcome.*', 'Welcome/index');
$router->map('GET', 'welcome/index/(\d+)', 'Welcome/index/$1');

include 'group-routes.php';
include 'argument-routes.php';
include 'middleware-routes.php';
// include 'filter-routes.php';

//--------------------------------------------------------------------
// Middleware is optional
//--------------------------------------------------------------------

$middleware = new Obullo\Router\Middleware(new SplQueue);
$middleware->register('\App\Middleware\\');

//--------------------------------------------------------------------
// Dispatch
//--------------------------------------------------------------------

$dispatcher = new Obullo\Router\Dispatcher($router->getPath(), $middleware);
// $dispatcher = new Obullo\Router\Dispatcher($router->getPath(), null);

$handler = null;
$dispatched = false;
$dispatcher->popGroup($request, $response, $router->getGroup());

foreach ($router->fetchRoutes() as $r) {

    if ($dispatcher->dispatch($r['pattern'])) {
        if (! in_array($request->getMethod(), (array)$r['method'])) {
            $middleware->queue('NotAllowed', (array)$r['method']);
            continue; // stop
        }
        if (! empty($r['middlewares'])) {
            print_r($r['middlewares']);
            /*
            foreach ((array)$r['middlewares'] as $value) {
                $middleware->queue($value['name'], $value['params']);
            }
            */
        }
        if (is_string($r['handler'])){
            $handler = $r['handler'];
        }
        if (is_callable($r['handler'])) {
            $handler = $r['handler']($request, $response, $dispatcher->getArgs());
        }
    }
}

// If routes is not restful do web routing functionality.

if ($handler == null && $router->restful() === false) {
    $handler = $router->getPath();
}
if ($handler != null) {
    $dispatched = true;
}

echo '<div style="font-size: 14px;">';
echo '<h3>Response</h3>';
echo '<hr size="1">';
echo '<pre>';
echo '<b>Handler Output: </b>';
if ($handler instanceof Zend\Diactoros\Response) {
    echo $handler->getBody().'<br>';
} else {
    var_dump($handler);
}
echo '<br>';
echo '<b>Arguments: </b>';
var_dump($dispatcher->getArgs());
echo '</pre>';
echo '</div>';