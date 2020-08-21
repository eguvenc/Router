<?php

return [
	'name' => [
		'path' => '/',
		'handler'=> 'App\Controller\DefaultController::index',
		'middleware' => 'App\Middleware\Dummy'
	],
	'dummy' => [
		'path' => '/<locale:locale>/dummy/<str:name>',
		'handler'=> 'App\Controller\DefaultController::dummy',
		'middleware' => [
        	'App\Middleware\Var',
        	'App\Middleware\Test',
        	'App\Middleware\Locale',
		]
	],
	'dummy_int' => [
		'path' => '/dummy/<str:name>/<int:id>',
		'host' => '<str:name>.example.com',
		'scheme' => ['http', 'https'],
		'handler'=> 'App\Controller\DefaultController::dummy',
	]
];