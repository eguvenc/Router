<?php

namespace Obullo\Router;

use Obullo\Router\Matcher\{
    PipeMatcher,
    RouteMatcher
};
use Obullo\Router\Generator;

/**
 * Router
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Router
{
    protected $path;
    protected $host;
    protected $pipe;
    protected $route;
    protected $method;
    protected $scheme;
    protected $matcher;
    protected $handler;
    protected $collection;
    protected $match = false;
    protected $routes = array();
    protected $middlewares = array();

    /**
     * Constructor
     *
     * @param RouteCollection $collection collection
     */
    public function __construct(RouteCollection $collection)
    {
        $request = $collection->getContext();
        $this->collection = $collection;
        $this->path = $request->getPath();
        $this->method = $request->getMethod();
        $this->host = $request->getHost();
        $this->scheme = $request->getScheme();
    }

    /**
     * Pipe process
     * 
     * @return void
     */
    public function popPipe()
    {
        $pipes = $this->collection->getPipes();
        if (empty($pipes)) {
            $this->routes = $this->collection->all();
            return;
        }
        foreach ($pipes as $pipe) {
            $matcher = new PipeMatcher($pipe);
            if ($matcher->matchScheme($this->scheme) && $matcher->matchHost($this->host) && $matcher->matchPath($this->path)) {
                foreach ($pipe->getRoutes() as $name => $route) {
                    $this->collection->add($name, $route);
                }
                $this->pipe = $pipe;
                $this->buildStack($pipe);
                $this->hostMatches = $matcher->getHostMatches();
            }
        }
        $this->routes = $this->collection->all();
    }

    /**
     * Route process
     * 
     * @return false|RouteInterface
     */
    public function popRoute()
    {
        if (empty($this->routes)) {
            return false;
        }
        $route = array_shift($this->routes);
        $matcher = new RouteMatcher($route);
        if ($matcher->matchScheme($this->scheme) && $matcher->matchHost($this->host) && $matcher->matchPath($this->path)) {
            $this->match = true;
            $this->matcher = $matcher;
            $args = $matcher->getArguments();
            $newArgs = $this->formatArguments($args);
            $route->setArguments($newArgs);
            $this->route = $route;
            $this->buildStack($route);
            return $route;
        }
        return $this->popRoute();
    }

    /**
     * Match
     * 
     * @param  string $path   path
     * @param  string $host   host optional
     * @param  mixed  $scheme scheme optional
     * @return false|RouteInterface
     */
    public function match(string $path, $host = null, $scheme = null)
    {
        $this->path = $path;
        if ($host != '') {
            $this->host = $host;
        }
        if ($scheme != '') {
            $this->scheme = $scheme;
        }
        $this->popPipe();
        return $this->popRoute();
    }

    /**
     * Dispatch request
     * 
     * @return false|RouteInterface
     */
    public function matchRequest()
    {
        $this->popPipe();
        return $this->popRoute();
    }

    /**
     * Returns to stack handler object
     * 
     * @return object
     */
    public function getStack() : array
    {
        return $this->middlewares;
    }

    /**
     * Returns to true if route match otherwise false
     * 
     * @return boolean
     */
    public function hasMatch() : bool
    {
        return $this->match;
    }

    /**
     * Returns to matched route
     * 
     * @return string
     */
    public function getMatchedRoute() : RouteInterface
    {
        return $this->route;
    }

    /**
     * Returns to matched route
     * 
     * @return string
     */
    public function getMatchedPipe()
    {
        return $this->pipe;
    }

    /**
     * Returns to matched host params
     * 
     * @return array
     */
    public function getHostMatches() : array
    {
        return $this->matcher->getHostMatches();
    }

    /**
     * Returns to route collection
     * 
     * @return object
     */
    public function getCollection() : RouteCollection
    {
        return $this->collection;
    }

    /**
     * Url generator helper
     * 
     * @param  string $name   route name
     * @param  array  $params url parameters
     * @return string
     */
    public function url(string $name, $params = array())
    {
        $generator = new Generator($this->getCollection());
        return $generator->generate($name, $params);
    }

    /**
     * Format arguments
     * 
     * @param $args matched arguments
     * 
     * @return array arguments
     */
    protected function formatArguments(array $args) : array
    {
        $newArgs = array();
        $types = $this->collection->getTypes();
        foreach ($args as $key => $value) {
            if (! is_numeric($key) && isset($types[$key])) {
                $newArgs[$key] = $types[$key]->toPhp($value);
            }
        }
        return $newArgs;
    }
            
    /**
     * Attach middlewares to stack object
     * 
     * @param RouteRule|RouteGroup $object route objects
     */
    protected function buildStack($object)
    {
        foreach ($object->getStack() as $value) {
            $this->middlewares[] = $value;
        }
    }
}