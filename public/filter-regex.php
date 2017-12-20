<?php

use Obullo\Router\AddFilter\Regex;

$router->group(
    'filter-regex/',
    function () use ($router) {

        $router->group(
            'test/',
            function () use ($router) {

                $router->get(
                    '(\w+)/(\d+).*',
                    function ($request, $response) use ($router) {
                        $response->getBody()->write("It works !");
                        return $response;
                    }

                )->filter(new Regex('.*?abc/(\d+)'))->add('Dummy');
            }
        );

    }
);