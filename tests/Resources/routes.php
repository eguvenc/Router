<?php

return [
	'home' => [
		'path'   => '/',
		'handler'=> 'App\Controller\DefaultController::index',
		'middleware' => 'App\Middleware\Dummy'
	],
	'dummy' => [
		'path'   => '/<locale:locale>/dummy/<str:name>',
		'handler'=> 'App\Controller\DefaultController::dummy',
	],
	'test_host' => [
		'host' => '<str:name>.example.com',
		'scheme' => ['http', 'https'],
		'path'   => '/dummy/<str:name>/<int:id>',
		'handler'=> 'App\Controller\DefaultController::dummy',
	],
	'user/' => [
		'middleware' => ['App\Middleware\Dummy'],
		'dummy' => [
			'path'   => '/dummy/<str:name>/<int:id>',
			'handler'=> 'App\Controller\DefaultController::dummy',
			'middleware' => 'App\Middleware\Dummy'
		],
		'lucky' => [
			'path' => '/lucky/<str:name>/<slug:slug>',
			'handler' => 'App\Controller\DefaultController::lucky',
		]
	],
	'test_host/' => [
		'host' => '<str:name>.example.com',
		'scheme' => ['http', 'https'],
		'dummy' => [
			'path'   => '/dummy/<str:name>/<int:id>',
			'handler'=> 'App\Controller\DefaultController::dummy',
		],
	]
];