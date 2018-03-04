<?php

namespace Obullo\Router;

use Obullo\Router\{
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
    protected $host;
    protected $routes = array();
    protected $schemes = array();
    protected $middlewares = array();

    /**
     * Constructor
     * 
     * @param string $pipe        pipe
     * @param array  $routes      routes
     * @param array  $middlewares middlewares
     */
    public function __construct(string $pipe, $middlewares = null, $host = null, $schemes = array())
    {
    	$this->pipe = ltrim($pipe, '/');
        $this->host = $host;
        $this->schemes = (array)$schemes;
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
        $route->setPipe($this->getPipe());
        $this->routes[$this->pipe.$name] = $route; 
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
     * Set host value
     * 
     * @param string $host host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * Returns to host
     * 
     * @return null|string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Returns to schemes
     * 
     * @return array
     */
    public function getSchemes() : array
    {
        return $this->schemes;
    }

    /**
     * Returns to middleware class names
     * 
     * @return array
     */
    public function getMiddlewares() : array
    {
        return $this->middlewares;
    }
}