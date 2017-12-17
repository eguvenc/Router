<?php

include 'header.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../vendor/autoload.php';

use Obullo\Router\Router;
use Obullo\Router\Dispatcher;
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

$router->map('GET', '/', 'WelcomeController->index');
$router->map('GET', 'welcome', 'WelcomeController->index');
$router->map('GET', 'welcome/index', 'WelcomeController->index');
$router->map('GET', 'welcome/index/(\d+)', 'WelcomeController->index');

include 'group-routes.php';
include 'argument-routes.php';
include 'middleware-routes.php';
include 'filter-contains.php';
include 'filter-not-contains.php';
include 'filter-regex.php';
include 'filter-not-regex.php';

//--------------------------------------------------------------------
// Dispatch
//--------------------------------------------------------------------

$dispatcher = new Dispatcher($request, $response, $router); // creates dispatcher with middleware functionality
$handler = $dispatcher->dispatch(
	new UrlMapper(
		$dispatcher,
		[
			'path' => $router->getPath(),
			'separator' => '->',
			'default.method' => 'index'
		]
	)
);
if ($handler instanceof Zend\Diactoros\Response) {
    $response = $handler;
}
if ($handler instanceof UrlMapperInterface) {
	$html = "<br /><br />";
	$html.= "<b>Class: </b>".$handler->getClass()."<br />";
	$html.= "<b>Method: </b>".$handler->getMethod()."<br />";
	$html.= "<b>First Argument: </b>".$handler->getArgs(0)."<br />";
	$response->getBody()->write($html);
}

echo '<h3>Pattern</h3>';
echo $router->getPattern();

/*
if ($router->hasMatch() && ! in_array($request->getMethod(), $dispatcher->getMethods())) {
	$queue->enqueue('NotAllowed', new Obullo\Middleware\Argument($dispatcher->getMethods()));
}
*/
echo '<h3>Response</h3>';
echo '<hr size="1">';
echo '<b>Handler Output: </b>';
echo '<pre>';
echo $response->getBody();
echo '</pre>';
echo '<br>';
echo '<b>Arguments: </b>';
echo '<pre>';
var_dump($dispatcher->getArgs());
echo '</pre>';

if ($queue instanceof QueueInterface) {
	echo '<pre>';
	var_dump($queue);
	echo '</pre>';
}