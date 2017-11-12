<?php

include 'header.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../vendor/autoload.php';

use Obullo\Router\Router;
use Obullo\Router\MiddlewareQueue;
use Obullo\Router\Dispatcher;

$request  = Zend\Diactoros\ServerRequestFactory::fromGlobals();
$response = new Zend\Diactoros\Response;

$router = new Router($request, $response);
$router->restful(false);

//--------------------------------------------------------------------
// Example routes
//--------------------------------------------------------------------

$router->rewrite('GET', '(?:en|de|es|tr)|/(.*)', '$1');  // example.com/en/  (or) // example.com/en

$router->map('GET', '/', 'Welcome/index');
$router->map('GET', '/welcome.*', 'Welcome/index');
$router->map('GET', '/welcome/index/(\d+)', 'Welcome/index/$1');

include 'group-routes.php';
include 'argument-routes.php';
include 'middleware-routes.php';
include 'filter-contains.php';
include 'filter-not-contains.php';
include 'filter-regex.php';
include 'filter-not-regex.php';

//--------------------------------------------------------------------
// $middlewareQueue is optional
//--------------------------------------------------------------------

$middlewareQueue = new MiddlewareQueue(new SplQueue);
$middlewareQueue->register('\App\Middleware\\');

//--------------------------------------------------------------------
// Dispatch
//--------------------------------------------------------------------

$dispatcher = new Dispatcher($request, $response, $router);

$dispatched = false;
$handler = $dispatcher->execute($middlewareQueue);
// $handler = $dispatcher->execute();


// If routes is not restful do web routing functionality.

if ($handler == null && $router->isRestful() == false) {
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

echo '<pre>';
var_dump($middlewareQueue);
echo '</pre>';