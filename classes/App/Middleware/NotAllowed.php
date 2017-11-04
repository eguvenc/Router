<?php

namespace App\Middleware;

use Zend\Diactoros\Stream;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class NotAllowed
{
    /**
     * Invoke middleware
     *
     * @param ServerRequestInterface $request  request
     * @param ResponseInterface      $response respone
     * @param callable               $next     callable
     * @param array                  $methods  allowed methods
     *
     * @return object ResponseInterface
     */
    public function __invoke(Request $request, Response $response, callable $next, $methods = array())
    {
        $error = sprintf(
            'Method must be one of : %s',
            implode(', ', $methods)
        );
        $stream = new Stream(fopen('php://temp', 'r+'));
        $stream->write($error);
            
        return $response
            ->withStatus(405)
            ->withHeader('Allow', implode(', ', $methods))
            ->withHeader('Content-Type', 'text/html')
            ->withBody($stream);
    }
}