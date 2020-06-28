<?php

namespace Obullo\Router;

use Obullo\Router\Pattern;
use Obullo\Router\RequestContext;
use Obullo\Router\Traits\RequestContextAwareTrait;
use Obullo\Router\Traits\ExceptionTrait;
use Obullo\Router\Exception\BadRouteException;
use Obullo\Router\Exception\UndefinedVariableException;
use ArrayIterator;
use IteratorAggregate;
use Countable;

/**
 * Route collection
 *
 * @copyright Obullo
 * @license   https://opensource.org/licenses/BSD-3-Clause
 */
class RouteCollection implements IteratorAggregate, Countable
{
    use RequestContextAwareTrait;

    protected $var = array();
    protected $config = array();
    protected $routes = array();
    protected $name;
    protected $pattern;

    /**
     * Construct parameters
     *
     * @param array $config configuration array
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->pattern = new Pattern($config);
    }

    /**
     * Return to configuration
     *
     * @return array
     */
    public function getConfig() : array
    {
        return $this->config;
    }

    /**
     * Add route
     *
     * @param RouteInterface $route object
     */
    public function add(RouteInterface $route)
    {
        $route->setPattern($this->pattern);
        $this->name = $route->getName();
        $route->convert();
        $this->routes[$this->name] = $route;

        return $this;
    }

    /**
     * Add variable
     *
     * @param string $name var name
     * @param array $data array data
     */
    public function addVariable(string $name, array $data)
    {
        $this->var[$name] = $data;
        return $this;
    }

    /**
     * Returns to variable data
     *
     * @param  string $name var name
     * @return @UndefinedVariableException|array var data
     */
    public function getVariable(string $name)
    {
        $name = '$'.ltrim($name, '$');
        if (false == isset($this->var[$name])) {
            throw new UndefinedVariableException(
                sprintf(
                    'The variable "%s" is not defined.',
                    $name
                )
            );
        }
        return $this->var[$name];
    }

    /**
     * Add host to current route
     *
     * @param string $host name
     */
    public function host($host) : Self
    {
        $this->routes[$this->name]->setHost($host);
        return $this;
    }

    /**
     * Add scheme to current route
     *
     * @param string|array scheme name
     */
    public function scheme($scheme) : Self
    {
        $this->routes[$this->name]->setSchemes($scheme);
        return $this;
    }

    /**
     * Add middleware to current route
     *
     * @param string|array middleware class name
     */
    public function middleware($middleware) : Self
    {
        $this->routes[$this->name]->middleware($middleware);
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
    public function get(string $name)
    {
        return isset($this->routes[$name]) ? $this->routes[$name] : false;
    }

    /**
     * Remove route
     *
     * @param  string $name name
     * @return void
     */
    public function remove(string $name)
    {
        unset($this->routes[$name]);
    }
}
