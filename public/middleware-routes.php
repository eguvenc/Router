<?php


$router->group(
    'middleware/',
    function () use ($router) {

        $router->group(
            'test/',
            function ($request, $response) use ($router) {
                return $response;
            }
        )->add('Dummy');
    }
);