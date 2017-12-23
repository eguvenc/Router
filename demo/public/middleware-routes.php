<?php


$router->group(
    'middleware/',
    function () use ($router) {

        $router->group(
            'test/',
            function ($request, $response) use ($router) {

                $router->get(
                    'dummy.*',
                    function ($request, $response, $mapper) use ($router) {
                        $response->getBody()->write("It works !");
                        return $response;
                    }
                )
                ->add('Dummy');
            }
        );

        $router->group(
            'group/',
            function ($request, $response) use ($router) {
                $router->get('test.*','MiddlewareController->test') ->add('Dummy');
            }
        );
    }
);




