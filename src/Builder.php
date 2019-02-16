<?php

namespace Obullo\Router;

use Obullo\Router\Pipe;
use Obullo\Router\Route;
use Obullo\Router\RouteCollection;
use Obullo\Router\Exception\BadRouteException;
use Obullo\Router\Exception\UndefinedRouteException;

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
                throw new UndefinedRouteException('There is no rule defined in the configuration file.');
            }
            if ($name != '/' && substr($name, -1) == '/') { // Pipes

                $pipe = new Pipe($name, $route);
                unset($route['host'], $route['scheme'], $route['middleware']);  // Remove native attributes to validate route names

                $keys = array_keys($route); // Get all route names of the current pipe
                foreach ($keys as $key) {
                    $isAttribute = Self::isAttribute($key);
                    if (false == $isAttribute && false == is_array($route[$key])) {
                        throw new BadRouteException(
                            sprintf(
                                'Router does not recognize the "%s" attribute under the "%s" pipe. Use "$" prefix to define custom attributes.',
                                $key,
                                $name
                            )
                        );
                    }
                    if (false == $isAttribute) {
                        Self::ValidateRoute($route[$key], $key);
                        $pipe->add($key, new Route($route[$key]));
                    }
                }
                $this->collection->addPipe($pipe);
            } else {  // routes

                Self::ValidateRoute($route, $name);
                $this->collection->add($name, new Route($route));
            }
        }
        return $this->collection;
    }

    /**
     * Check route name is attribute
     *
     * @param  string  $key name
     * @return boolean
     */
    protected static function isAttribute($key)
    {
        return strpos($key, '$') === 0;
    }

    /**
     * Validate route
     *
     * @param  array  $route route
     * @return void
     */
    protected static function validateRoute(array $route, string $name)
    {
        if (empty($route['path'])) {
            throw new BadRouteException(
                sprintf(
                    'Route path is undefined in "%s" route.',
                    $name
                )
            );
        }
        if (empty($route['handler'])) {
            throw new BadRouteException(
                sprintf(
                    'Route handler is undefined in "%s" route.',
                    $name
                )
            );
        }
    }
}
