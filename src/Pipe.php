<?php

namespace Obullo\Router;

use Obullo\Router\{
    Route\RouteInterface,
	Stack\StackAwareTrait,
	Stack\StackAwareInterface
};
/**
 * Route pipe
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Pipe implements PipeInterface, StackAwareInterface
{
    use StackAwareTrait;

    protected $pipe;
    protected $routes = array();
    protected $middlewares = array();

    /**
     * Constructor
     * 
     * @param string $pipe        pipe
     * @param array  $routes      routes
     * @param array  $middlewares middlewares
     */
    public function __construct(string $pipe, $middlewares = null)
    {
    	$this->pipe = $pipe;
    	$this->middlewares = (array)$middlewares;
    }

    /**
     * Add route
     * 
     * @param string         $name  route name
     * @param RouteInterface $route route object
     */
    public function add(string $name, RouteInterface $route)
    {
        $this->routes[$this->pipe.$name] = $route->setPipe($this->getPipe());
    }

    /**
     * Returns to pipe
     * 
     * @return string
     */
    public function getPipe() : string
    {
    	return $this->pipe;
    }
    
    /**
     * Returns to routes
     *
     * @return array
     */
    public function getRoutes() : array
    {
        return $this->routes;
    }

    /**
     * Returns to matched routes
     * 
     * @param  string $path path
     * @return array|false
     */
    public function match(string $path)
    {
    	$path = ltrim($path, '/');
    	$matchedUri = substr($path, 0, strlen($this->pipe));
    	if ($this->pipe == $matchedUri) {
    		return $this->getRoutes();
    	}
    	return false;
    }

}