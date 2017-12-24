<?php

include 'header.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../../vendor/autoload.php';

use Obullo\Router\Router;
use Obullo\Router\UrlMapper;
use Obullo\Router\UrlMapperInterface;

use Obullo\Middleware\Queue;
use Obullo\Middleware\QueueInterface;

$request  = Zend\Diactoros\ServerRequestFactory::fromGlobals();
$response = new Zend\Diactoros\Response;

//--------------------------------------------------------------------
// MiddlewareQueue is optional
//--------------------------------------------------------------------

$queue = new Queue;
$queue->register('\App\Middleware\\');

//--------------------------------------------------------------------
// Router
//--------------------------------------------------------------------

$router = new Router($request, $response, $queue);

//--------------------------------------------------------------------
// Example routes
//--------------------------------------------------------------------

$router->rewrite('GET', '(?:en|de|es|tr)|/(.*)', '$1');  // example.com/en/  (or) // example.com/en

$router->get('/', 'WelcomeController->index');
$router->get('welcome', 'WelcomeController->index');
$router->get('welcome/index', 'WelcomeController->index');
$router->get('welcome/index/(\d+)', 'WelcomeController->index');

include 'argument-routes.php';
include 'filter-regex.php';
include 'group-routes.php';
include 'middleware-routes.php';

//--------------------------------------------------------------------
// Dispatch
//--------------------------------------------------------------------

$mapper  = new UrlMapper($router);
$handler = $mapper->dispatch();

if (is_callable($handler)) {
    $handler = $handler($request, $response, $mapper);
}
if ($handler instanceof Zend\Diactoros\Response) {
    $response = $handler;
}
if (is_string($handler)) {
	echo '<h3>String Handler</h3>';
	echo $handler;
}

echo '<h3>Pattern</h3>';
echo '<pre>';
echo $mapper->getPattern();
echo '</pre>';

echo '<h3>Arguments</h3>';
echo '<pre>';
print_r($mapper->getArgs());
echo '</pre>';
/*
if ($router->hasMatch() && ! in_array($request->getMethod(), $mapper->getMethods())) {
	$queue->enqueue('NotAllowed', new Obullo\Middleware\Argument($mapper->getMethods()));
}
*/
echo '<h3>Response</h3>';
echo '<hr size="1">';
echo '<pre>';
echo $response->getBody();
echo '</pre>';

if ($queue instanceof QueueInterface) {
	echo '<h3>Middleware</h3>';
	echo '<pre>';
	var_dump($queue);
	echo '</pre>';
}

