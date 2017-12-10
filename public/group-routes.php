<?php


// /group/test/a/1

$router->group(
    'group/',
    function () use ($router) {
        
        $router->group(
            'test/',
            function () use ($router) {

                $router->map(
                    'GET',
                    '/(\w+)/(\d+).*',
                    function ($request, $response, $args = null) use ($router) {
                    
                        $response->getBody()->write("It works !");

                        return $response;
                    }
                );
            }
        );
    }
);