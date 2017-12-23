<?php

namespace App\Middleware;

use Zend\Diactoros\PhpInputStream;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ParsedBody
{
    /**
     * Invoke middleware
     * 
     * @param ServerRequestInterface $request  request
     * @param ResponseInterface      $response response
     * @param callable               $next     callable
     * 
     * @return object ResponseInterface
     */
    public function __invoke(Request $request, Response $response, callable $next = null)
    {
        $parsedBody = $request->getParsedBody();

        if (empty($parsedBody)) {

            $body = (string)new PhpInputStream('php://input');
            $mediaType = $this->getMediaType($request);

            switch ($mediaType) {
            case 'application/json':
                $parsedBody = json_decode($body, true);
                break;
            case 'application/xml':
                $parsedBody = simplexml_load_string($body);
                break;
            }
            $request = $request->withParsedBody($parsedBody);
        }
        
        return $next($request, $response);
    }

    /**
     * Get request media type, if known.
     *
     * @param ServerRequestInterface $request request
     * 
     * @return string|null The request media type, minus content-type params
     */
    protected function getMediaType(Request $request)
    {
        $contentType = $this->getContentType($request);

        if ($contentType) {
            $contentTypeParts = preg_split('/\s*[;,]\s*/', $contentType);
            return strtolower($contentTypeParts[0]);
        }
        return null;
    }

    /**
     * Get request content type.
     *
     * @param ServerRequestInterface $request request
     * 
     * @return string|null The request content type, if known
     */
    protected function getContentType(Request $request)
    {
        $result = $request->getHeader('Content-Type');

        return $result ? $result[0] : null;
    }
}