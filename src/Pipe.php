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
    protected $routes = array();  // route collection
    protected $attributes = array(); // pipe attributes
    protected $schemes = array();
    protected $middlewares = array();

    /**
     * Constructor
     * 
     * @param string $pipe   pipe
     * @param array  $attributes attributes
     */
    public function __construct(string $pipe, array $attributes)
    {
    	$this->pipe = ltrim($pipe, '/');
        $this->host = empty($attributes['host']) ? null : $attributes['host'];
        $this->schemes = empty($attributes['scheme']) ? null : (array)$attributes['scheme'];
    	$this->attributes = $attributes;
        $this->middlewares = empty($attributes['middleware']) ? array() : (array)$middlewares; 
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
     * Set pipe attribute
     * 
     * @param string $key   string
     * @param mixed  $value value
     */
    public function setAttribute(string $key, $value)
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Returns to pipe attribute
     * 
     * @param  string $key name
     * @return mixed value
     */
    public function getAttribute(string $key)
    {
        return isset($this->attributes[$key]) ? $this->attributes[$key] : null;
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
}