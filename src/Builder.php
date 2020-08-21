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
 * @license   https://opensource.org/licenses/BSD-3-Clause
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
        foreach ($datas as $name => $data) {
            if (! is_array($data)) {
                throw new UndefinedRouteException('There is no rule defined in the route configuration file.');
            }
            Self::validateRoute($name, $data);
            $path = $data['path'];
            $handler = $data['handler'];
            $method  = isset($data['method']) ? $data['method'] : 'GET';
            $host = isset($data['host']) ? $data['host'] : null;
            $scheme = isset($data['scheme']) ? $data['scheme'] : array();
            $middleware = isset($data['middleware']) ? $data['middleware'] : array();

            $this->collection->add($name, new Route($method, $path, $handler))
                ->host($host)
                ->scheme($scheme)
                ->middleware($middleware);
        }
        return $this->collection;
    }

    /**
     * Validate route
     *
     * @param  string $name route name
     * @param  array  $data route data
     * @return void
     */
    protected static function validateRoute(string $name, array $data)
    {
        if (empty($name)) {
            throw new BadRouteException('You must provide a route name.');
        }
        if (empty($data['path'])) {
            throw new BadRouteException(
                sprintf(
                    'Route path is undefined for "%s" route.',
                    $name
                )
            );
        }
        if (empty($data['handler'])) {
            throw new BadRouteException(
                sprintf(
                    'Route handler is undefined for "%s" route.',
                    $name
                )
            );
        }
    }
}
