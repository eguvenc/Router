<?php


$router->group(
    'filter-regex/',
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

                )->filter('regex', '.*?abc/(\d+)')->add('Dummy');
            }
        );

    }
);