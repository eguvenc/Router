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

            echo '<pre>'.print_r($route, true).'</pre>';

            if (! is_array($route)) {
                throw new BadRouteException('There is no rule defined in the configuration file.');
            }
            if ($name != '/' && substr($name, -1) == '/') { // pipes

                $pipe = new Pipe($name, $route);
                unset($route['host'], $route['scheme']);

                $keys = array_keys($route); // Get all route names of the current pipe
                foreach ($keys as $key) {
                    $isAttribute = Self::isAttribute($key);
                    if (false == $isAttribute && false == is_array($route[$key])) {
                        throw new UndefinedRouteNameException(
                            sprintf(
                                'There is an unknown route name under the "%s" pipe.',
                                $name
                            )
                        );
                    }
                    if (false == $isAttribute) {
                        Self::ValidateRoute($route[$key]);
                        $pipe->add($key, new Route($route[$key]));
                    }
                }
                $this->collection->addPipe($pipe);

            } else {  // routes

                Self::ValidateRoute($route);
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
    protected function isAttribute($key)
    {
        return strpos($key, '$') === 0;
    }

    /**
     * Validate route
     * 
     * @param  array  $route route
     * @return void
     */
    protected static function validateRoute(array $route)
    {
        if (empty($route['path'])) {
            throw new BadRouteException(
                sprintf(
                    'Route path is undefined in %s route.',
                    $route['name']
                )
            );
        }
        if (empty($route['handler'])) {
            throw new BadRouteException('Route handler is undefined.');
        }
    }
}