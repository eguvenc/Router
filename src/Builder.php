<?php

namespace Obullo\Router;

use Obullo\Router\Route;
use Obullo\Router\RouteCollection;
use Obullo\Router\Exception\BadRouteException;
use Obullo\Router\Exception\UndefinedRouteException;
use Obullo\Router\Exception\InvalidKeyException;

/**
 * Build route data from configuration file
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Builder
{
    protected $collection;
    
    /**
     * Constructor
     *
     * @param RouteCollection $collection collection
     */
    public function __construct(RouteCollection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * Build routes
     *
     * @param  array  $datas rotues
     * @return RouteCollection object
     */
    public function build(array $datas) : RouteCollection
    {
        foreach ($datas as $key => $data) {
            if (strpos($key, '$') === 0) {
                $this->collection->addVariable($key, $data);
            } else {
                if (! is_array($data)) {
                    throw new UndefinedRouteException('There is no rule defined in the route configuration file.');
                }
                Self::validateRoute($key, $data);
                $handler = $data['handler'];
                $method  = isset($data['method']) ? $data['method'] : 'GET';
                $host = isset($data['host']) ? $data['host'] : null;
                $scheme = isset($data['scheme']) ? $data['scheme'] : array();

                $middleware = array();
                if (isset($data['middleware'])) {
                    $middleware = $this->parseMiddlewareVar($data['middleware']);
                }
                $this->collection->add(new Route($method, $key, $handler))
                    ->host($host)
                    ->scheme($scheme)
                    ->middleware($middleware);
            }
        }
        return $this->collection;
    }

    /**
     * Parse variables for middleware key
     *
     * @param  mixed $middlewares middlewares
     * @return array
     */
    protected function parseMiddlewareVar($middlewares) : array
    {
        $newMiddlewares = array();
        foreach ((array)$middlewares as $middleware) {
            if (strpos($middleware, '$') === 0) {
                $var = $this->collection->getVariable($middleware);
                if (false == isset($var['middleware'])) {
                    throw new InvalidKeyException(
                        sprintf(
                            'The "%s" variable hasn\'t got a middleware key.',
                            $middleware
                        )
                    );
                }
                $newMiddlewares = array_merge($newMiddlewares, $var['middleware']);
            } else {
                $newMiddlewares[] = $middleware;
            }
        }
        return $newMiddlewares;
    }

    /**
     * Validate route
     *
     * @param  array  $data route
     * @return void
     */
    protected static function validateRoute(string $path, array $data)
    {
        if (empty($data['handler'])) {
            throw new BadRouteException(
                sprintf(
                    'Route handler is undefined for "%s" path.',
                    htmlspecialchars($path)
                )
            );
        }
    }
}
