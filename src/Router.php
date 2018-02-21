<?php

namespace Obullo\Router;

use Obullo\Router\Exception\UndefinedTypeException;

/**
 * Router
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Router
{
    protected $path;
    protected $route;
    protected $handler;
    protected $methods;
    protected $collection;
    protected $match = false;
    protected $pipes = array();
    protected $routes = array();
    protected $requestContext;
    protected $middlewares = array();
    protected $pipeMatches = array();

    /**
     * Constructor
     * 
     * @param RequestContext  $context    context
     * @param RouteCollection $collection collection
     */
    public function __construct(RequestContext $context, RouteCollection $collection)
    {
        $this->collection = $collection;
        $this->requestContext = $context;
        $this->path = $context->getPath();
    }

    /**
     * Pop process
     * 
     * @return void
     */
    public function popPipe()
    {
        $count = $this->collection->pipeCount();
        if (0 === $count) {
            return;
        }
        $pipes = $this->collection->pipeAll();
        foreach ($pipes as $pipe) {
            if ($routes = $pipe->match($this->path)) {
                $this->buildStack($pipe);
            }
        }
    }

    /**
     * Route process
     * 
     * @return null|RouteInterface
     */
    public function popRoute()
    {
        $count = $this->collection->count();
        if (0 === $count OR empty($this->routes)) {
            return;
        }
        $route   = array_shift($this->routes);
        $pattern = $route->getPattern();
        $regex   = '#^'.$pattern.'$#';
        $args = array();
        if ($this->getPath() == $pattern OR preg_match($regex, $this->getPath(), $args)) {
            array_shift($args);
            $this->match = true;
            $newArgs = $this->formatArgs($args);
            $route->setArgs($newArgs);
            $this->route = $route;
            $this->buildStack($route);
            return $route;
        }
        return $this->popRoute();
    }

    /**
     * Dispatch request
     * 
     * @return object
     */
    public function matchRequest() : bool
    {
        $this->popPipe();
        $this->routes = $this->collection->all();
        $route = $this->popRoute();
        if ($this->match) {
            $this->handler = $route->getHandler();
            $this->methods = $route->getMethods();
        }
        return $this->match;
    }

    /**
     * Returns to path
     * 
     * @return string
     */
    public function getPath()
    {
        return $this->path;
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
     * Returns to handler
     * 
     * @return mixed
     */
    public function getMatchedHandler()
    {
        return $this->handler;
    }

    /**
     * Format arguments
     * 
     * @param $args matched arguments
     * 
     * @return array arguments
     */
    protected function formatArgs(array $args) : array
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