<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Dummy
{
    /**
     * Execute the middleware
     * 
     * @param ServerRequestInterface $request  request
     * @param ResponseInterface      $response response
     *
     * @return ResponseInterface
     */
    public function __invoke(Request $request, Response $response)
    {
        return $response;
    }
}