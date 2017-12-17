<?php


$router->group(
    'filter-not-contains/',
    function () use ($router) {

        $router->group(
            'test/',
            function () use ($router) {

                $router->map(
                    'GET',
                    '(\w+)/(\d+).*',
                    function ($request, $response) use ($router) {
                        $response->getBody()->write("It works !");
                        return $response;
                    }

                )->filter('notContains', ['test/foo/888', 'test/foo/999'])->add('Dummy');
            }
        );
    }
);