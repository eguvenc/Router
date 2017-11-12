<?php


$router->group(
    'filter-contains/',
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

                )->filter('contains', ['test/foo/123', 'test/foo/1234'])->add('Dummy');
            }
        );
    }
);