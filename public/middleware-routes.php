<?php


$router->group(
    'middleware/',
    function () use ($router) {

        $router->group(
            'test/',
            function ($request, $response) use ($router) {

                    $router->map(
                        'GET',
                        'dummy.*',
                        function ($request, $response, $args = null) use ($router) {
                            $response->getBody()->write("It works !");
                            return $response;
                        }
                    )
                    ->add('Dummy');
            }
        );
    }
);


$router->group(
    'middleware/',
    function () use ($router) {

        $router->group(
            'group/',
            function ($request, $response) use ($router) {

                $router->map(
                    'GET',
                    'test.*', function(){
                    
                })->add('Dummy');
                
                $response->getBody()->write("It works !");
                return $response;
            }
        );
    }
);
