<?php


// /group/test/a/1

$router->group(
    'group/',
    function () use ($router) {
        
        $router->group(
            'test/',
            function ($request, $response, $folder) use ($router) {

                $router->get(
                    '(\w+)/(\d+).*',
                    function ($request, $response, $mapper) use ($router) {
                        $response->getBody()->write("It works !");
                        return $response;
                    }
                );
            }
        );
    }
);