<?php

use Obullo\Router;

$request  = Zend\Diactoros\ServerRequestFactory::fromGlobals();
$response = new Zend\Diactoros\Response;

$router = new Router($request, $response, array('middleware.classes' => '\\App\Middleware\\'));

$router->restful(false);  // disable web routing style
$router->rewrite('GET', '(?:en|de|es|tr)|/(.*)', '$1');  // example.com/en/  (or) // example.com/en


// $router->map('GET', '/users/(.*)',
//     function ($request, $response, $args) use($router) {

//         $response->getBody()->write('Users group');

//         // $this->add();

//         return $response;
//     }
// );

// $router->map('GET', '/newEntry', 'NewEntry/index');

$router->map('GET', '/', 'Welcome/index');
$router->map('GET', 'welcome', 'Welcome/index');
$router->map('GET', 'welcome/index/(\d+)', 'Welcome/index/$1');

// $router->map('GET', 'welcome/index/(?<id>\d+)/(?<month>\w+)', 'Welcome/index/$1/$2');

/*
$router->map('GET', 'welcome/index/(?<id>\d+)/(?<month>\w+)', function($request, $response) {
    $response->getBody()->write( print_r($request->getArgs(), true));
    return $response;
});
*/


// $router->map('GET', 'test', 'Test/Test/index');
// $router->map('GET', 'test/test/index', 'Test/Test/index');
// $router->map('GET', 'test/test/hello', 'Test/Test/hello');

// $router->map('GET', '/users/(\w+)/(\d+)', '/Users/$1/$2');
// $router->map('GET', '/users/(\w+)/(\d+)', function ($request, $response) {
//     var_dump($args);
// });

$router->group(
    'users/',
    function () use ($router) {

        $router->group(
            'test/',
            function () use ($router) {

                // $router->map('GET', 'users/test/(\w+)/(\d+)', 'Welcome/index/$1/$2');

                // throw new \Exception("cimcime");
                
                $router->map(
                    'GET',
                    'users/test/(\w+)/(\d+).*',
                    function ($request, $response) {
                        
                        // var_dump($request->getArgs());
                        
                        $response->getBody()->write("yES !");

                        return $response;
                    }
                )->add('Guest');


                //->filter('contains', ['users/test/45'])->add('Guest');

                //->filter('notContains', ['users/teZ'])->add('Guest');;
                //
                // ->ifContains(['login'])
                // ->ifNotContains(['login', 'payment'])
                // ->ifRegExp(['welcome/path/index'])
                // ->ifNotRegExp(['welcome/path/index'])
            }
        );
    }
);


$handler = $this->router->getHandler();   // Returns to route handler (callable or string)
$args    = $this->router->getArgs();	  // Returns to mapped route arguments
// $queue   = $this->router->getQueue();  // Returns to middleware queue object

var_dump($handler);
