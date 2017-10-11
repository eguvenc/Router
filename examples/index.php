<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../vendor/autoload.php';

use Obullo\Router\Router;

$request  = Zend\Diactoros\ServerRequestFactory::fromGlobals();
$response = new Zend\Diactoros\Response;

$router = new Router($request->getUri()->getPath(), $request->getMethod(), $request->getServerParams());

$router->restful(true);

//--------------------------------------------------------------------
// Rewrite rules to run /example folder
//--------------------------------------------------------------------

$router->rewrite(array('GET','POST'), '/examples/', '$1');
$router->rewrite(array('GET','POST'), '/examples/index.php(.*)', '$1');

//--------------------------------------------------------------------
// Example routes
//--------------------------------------------------------------------

$router->map('GET', '/', 'Welcome/index');
$router->map('GET', 'welcome', 'Welcome/index');
$router->map('GET', 'welcome/index/(\d+)', 'Welcome/index/$1');

//--------------------------------------------------------------------
// Middleware is Optional
//--------------------------------------------------------------------

$middleware = new Obullo\Router\Middleware(new SplQueue);
$middleware->register('\App\Middleware\\');

//--------------------------------------------------------------------
// Dispatch
//--------------------------------------------------------------------

$dispatcher = new Obullo\Router\Dispatcher($router->getPath(), $middleware);

$handler = null;
$dispatched = false;
$dispatcher->popGroup($request, $response, $router->getGroup());

foreach ($router->getRoutes() as $r) {
    if ($dispatcher->dispatch($r['pattern'])) {
        if (! in_array($request->getMethod(), (array)$r['method'])) {
            $middleware->queue('NotAllowed', (array)$r['method']);
            continue; // stop
        }
        if (! empty($r['middlewares'])) {
            print_r($r['middlewares']);
            foreach ((array)$r['middlewares'] as $value) {
                $middleware->queue($value['name'], $value['params']);
            }
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


var_dump($handler);