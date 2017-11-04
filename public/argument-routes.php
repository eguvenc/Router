<?php

// /arg/test/10/october

$router->map('GET', 'arg/test/(?<id>\d+)/(?<foo>\w+)', function($request, $response, $args) use($router) {
    $response->getBody()->write( print_r($args, true));
    return $response;
});