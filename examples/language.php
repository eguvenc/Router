<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../vendor/autoload.php';

use Obullo\Router\Router;

$request  = Zend\Diactoros\ServerRequestFactory::fromGlobals();
$response = new Zend\Diactoros\Response;

$router = new Router($request->getUri()->getPath(), $request->getMethod(), $request->getServerParams());

$router->restful(true);  // only matched routes enabled
$router->rewrite(array('GET','POST'), '/examples/index.php(.*)', '$1');  // rewrite rule for examples folder

$router->rewrite('GET', '(?:en|de|es|tr)|/(.*)', '$1');  // example.com/en/  (or) // example.com/en

$router->map('GET', '/', 'Welcome/index');
$router->map('GET', 'welcome', 'Welcome/index');