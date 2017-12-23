<?php

// /arg/test/10/october

$router->get('arg/test/(?<id>\d+)/(?<foo>\w+)', function($request, $response, $mapper) use($router) {
    $response->getBody()->write(print_r($mapper->getArgs(), true));
    return $response;
});