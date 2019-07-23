<?php

namespace Obullo\Router;

use Obullo\Router\Pattern;
use Obullo\Router\RequestContext;
use Obullo\Router\Traits\RequestContextAwareTrait;
use Obullo\Router\Exception\BadRouteException;
use ArrayIterator;
use IteratorAggregate;
use Countable;

/**
 * Route collection
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class RouteCollection implements IteratorAggregate, Countable
{
    use RequestContextAwareTrait;

    protected $routes = array();
    protected $path;
    protected $pattern;

    /**
     * Constructor
     * 
     * @param Pattern $pattern object
     */
    public function __construct(Pattern $pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * Add route
     *
     * @param RouteInterface $route object
     */
    public function add(RouteInterface $route) : Self
    {
        $route->setPattern($this->pattern);
        $this->path = $route->getPath();
        $route->convert();
        $this->routes[$this->path] = $route;

        return $this;
    }

    /**
     * Add host to current route
     * 
     * @param string $host name
     */
    public function addHost(string $host)
    {
        $this->routes[$this->path]->setHost($host);
        return $this;
    }

    /**
     * Add scheme to current route
     * 
     * @param string|array scheme name
     */
    public function addScheme($scheme)
    {
        $this->routes[$this->path]->setScheme($host);
        return $this;
    }

    /**
     * Add middleware to current route
     * 
     * @param string|array middleware class name
     */
    public function addMiddleware($middleware)
    {
        $this->routes[$this->path]->addMiddleware($middleware);
        return $this;
    }

    /**
     * Returns to number of routes
     *
     * @return int
     */
    public function count() : int
    {
        return count($this->routes);
    }

    /**
     * Returns to all routes
     *
     * @return array
     */
    public function all() : array
    {
        return $this->routes;
    }

    /**
     * Gets the current RouteCollection as an Iterator that includes all routes.
     *
     * It implements IteratorAggregate.
     *
     * @return ArrayIterator object
     */
    public function getIterator()
    {
        return new ArrayIterator($this->routes);
    }

    /**
     * Returns to pattern object
     *
     * @return array
     */
    public function getPattern() : Pattern
    {
        return $this->pattern;
    }

    /**
     * Returns to selected route
     *
     * @param  string $name name
     * @return boolean
     */
    public function get(string $path)
    {
        return isset($this->routes[$path]) ? $this->routes[$path] : false;
    }

    /**
     * Remove route
     *
     * @param  string $name name
     * @return void
     */
    public function remove(string $path)
    {
        unset($this->routes[$path]);
    }
}
