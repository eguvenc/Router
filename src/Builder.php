<?php

namespace Obullo\Router;

use Obullo\Router\{
    Pipe,
    Route,
    RouteCollection,
    Exception\BadRouteException,
    Exception\UndefinedRouteNameException
};

/**
 * Build route data
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
     * @param  array  $routes rotues
     * @return RouteCollection object
     */
    public function build(array $routes) : RouteCollection
    {    
        foreach ($routes as $name => $route) {
            if (! is_array($route)) {
                throw new BadRouteException('A route rule does not have route name in your routes.');
            }
            if ($name != '/' && substr($name, -1) == '/') { // pipes
                $pipe = new Pipe($name, Self::getMiddlewares($route), Self::getHost($route), Self::getScheme($route));
                unset($route['middleware'], $route['host'], $route['scheme']);

                $keys = array_keys($route);
                foreach ($keys as $key) {
                    if (! is_array($route[$key])) {
                        throw new UndefinedRouteNameException(
                            sprintf(
                                'There is a undefined route name under the "%s" pipe.',
                                $name
                            )
                        );
                    }
                    Self::ValidateRoute($key, $route[$key]);
                    $method = isset($route[$key]['method']) ? $route[$key]['method'] : 'GET';
                    $pipe->add(
                        $key,
                        new Route(
                            $method,
                            $route[$key]['path'],
                            $route[$key]['handler'],
                            Self::getMiddlewares($route[$key]),
                            Self::getHost($route[$key]),
                            Self::getScheme($route[$key])
                        )
                    );
                }
                $this->collection->addPipe($pipe);
            } else {  // routes
                Self::ValidateRoute($name, $route);
                $method = isset($route['method']) ? $route['method'] : 'GET';
                $this->collection->add(
                    $name,
                    new Route(
                        $method,
                        $route['path'],
                        $route['handler'],
                        Self::getMiddlewares($route),
                        Self::getHost($route),
                        Self::getScheme($route)
                    )
                );
            }
        }
        return $this->collection;
    }

    /**
     * Returns to array
     * 
     * @param  array  $route route
     * @return array
     */
    protected static function getMiddlewares(array $route) : array
    {
        if (empty($route['middleware'])) {
            return array();
        }
        return (array)$route['middleware'];
    }

    /**
     * Returns to host
     * 
     * @param  array  $route array
     * @return null|string
     */
    protected static function getHost(array $route)
    {
        if (empty($route['host'])) {
            return;
        }
        return $route['host'];
    }

    /**
     * Returns to uri scheme
     * 
     * @param  array  $route array
     * @return null|string
     */
    protected static function getScheme(array $route)
    {
        if (empty($route['scheme'])) {
            return;
        }
        return $route['scheme'];
    }

    /**
     * Validate route

     * @param  string $name  name
     * 
     * @return void
     */
    protected static function validateRoute(string $name, $route)
    {
        if (empty($route['path'])) {
            throw new BadRouteException('Route path is undefined.');
        }
        if (empty($route['handler'])) {
            throw new BadRouteException('Route handler is undefined.');
        }
    }
}