<?php


$router->group(
    'filter-not-regex/',
    function () use ($router) {

        $router->group(
            'test/',
            function () use ($router) {

                $router->map(
                    'GET',
                    '(\w+)/(.*)',
                    function ($request, $response) use ($router) {
                        
                        $response->getBody()->write("It works !");

                        return $response;
                    }

                )->filter('notRegex', '.*?abc/(\d+)')->add('Dummy');
            }
        );

    }
);