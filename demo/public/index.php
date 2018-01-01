<?php

// include 'header.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../../vendor/autoload.php';

use Obullo\Router\RouteCollection;
// use Obullo\Router\RewriteRule;
use Obullo\Router\RouteRule;
use Obullo\Router\RouteMapper;
use Obullo\Router\Router;

use Obullo\Middleware\Queue as Middleware;
use Obullo\Middleware\QueueInterface;

$request  = Zend\Diactoros\ServerRequestFactory::fromGlobals();
$response = new Zend\Diactoros\Response;

$start = microtime(true);

/*
/
/welcome
/welcome
/welcome/index/{id:\d+}/{name:\w+}
/welcome/index/id/name
*/

//--------------------------------------------------------------------
// MiddlewareQueue is optional
//--------------------------------------------------------------------

/*
$regexRule = '#^(?||welcome|welcome/index|welcome/index/(\d+)|welcome/index/(\d+)/(\w+)|)$#x';
var_dump(preg_match($regexRule, "welcome/index/1/abc", $matches));
print_r($matches);
die;
*/
$queue = new Middleware;
$queue->register('\App\Middleware\\');

use Obullo\Router\Pattern\StrPattern;
use Obullo\Router\Pattern\IntPattern;
use Obullo\Router\Pattern\NumberPattern;

$patterns = array(
	new IntPattern('<id:int>'),  // \d+
	new StrPattern('<name:str>'),	   // \w+
	// new NumberPattern('<number:str>'),  // \d+
	// new TruePattern("<true:bool>"),
	// new FalsePattern("<false:bool>"),
	// new DecimalPattern("<decimal:float>"),
	// "<year:str>", // [0-9]{4}
	// "<month:str>", // [0-9]{2}
	// "<timestamp:int>",
	// "<alfa:str>",
	// "<alnum:str>",
	// "<alnum_dash:str>",
	// "<slug:str>",
	// "<slug:str>",
	// "<uuid:str>",
	// "<any:any>",
);

// "articles/<year:int>/<month:>/";
// "articles/[0-9]{4}/[0-9]{2}"
// "articles/2004/04" <==> "articles/<int:year>/<int:month>";

$collection = new RouteCollection($patterns);
$collection->setUriPath($request->getUri()->getPath());

// $collection->attach(new RewriteRule('GET', '(?:en|de|es|tr)|/(.*)', '$1'));
$collection->attach(new RouteRule('GET', '/', 'WelcomeController->index'));
$collection->attach(new RouteRule('GET', 'welcome', 'WelcomeController->index'));
$collection->attach(new RouteRule('GET', 'welcome/index/<id:int>', 'WelcomeController->index'));
$collection->attach(new RouteRule('GET', 'welcome/index/<id:int>/<name:str>', 'WelcomeController->index'));
// $collection->attach(new RouteRule('GET', 'welcome/index/<any:any>', 'WelcomeController->index'));
// $collection->attach(new RouteRule('GET', 'arg/test/<id:int>/<name:str>', 'WelcomeController->index'));


/*
$collection->attach(new RouteGroup('users/', function() use($collection) {
	$collection->attach(new RouteGroup('test/', function() use($collection) {
		$collection->attach(new RouteRule('GET', '(\w+)/(\d+).*', function($request, $response, $mapper) {
			$response->getBody()->write("It works !");
            return $response;
		}));
	}));
}));
*/
//--------------------------------------------------------------------
// Router
//--------------------------------------------------------------------
// $router = new Router($collection);
// $router->setQueue($queue);

// $router = new Router($request, $response, $queue);

//--------------------------------------------------------------------
// Example routes
//--------------------------------------------------------------------

/*
$router->rewrite('GET', '(?:en|de|es|tr)|/(.*)', '$1');  // example.com/en/  (or) // example.com/en
$router->get(new Route('/', 'WelcomeController->index')));
$router->get(new Route('welcome', 'WelcomeController->index'));
$router->get(new Route('welcome/index', 'WelcomeController->index'));
$router->get(new Route('welcome/index/(\d+)', 'WelcomeController->index'));
*/
/*
include 'argument-routes.php';
include 'filter-regex.php';
include 'group-routes.php';
include 'middleware-routes.php';
*/
//--------------------------------------------------------------------
// Dispatch
//--------------------------------------------------------------------

$mapper  = new RouteMapper($collection);
$handler = $mapper->mapCurrentRequest();

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

$end = microtime(true) - $start;
echo number_format($end, 4);


// echo '<h3>Pattern</h3>';
// echo '<pre>';
// echo $mapper->getPattern();
// echo '</pre>';

echo '<h3>Arguments</h3>';
echo '<pre>';
var_dump($mapper->getArgs());
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

