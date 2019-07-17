<?php

namespace Obullo\Router;

use Obullo\Router\Traits\StackAwareTrait;
use Obullo\Router\Traits\AttributeAwareTrait;

/**
 * Route pipe
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Pipe implements PipeInterface
{
    use StackAwareTrait;
    use AttributeAwareTrait;

    protected $pipe;
    protected $host;
    protected $routes = array();  // route collection
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
        $this->pipe = '/'.trim($pipe, '/').'/';  // normalize pipe
        $this->host = empty($attributes['host']) ? null : $attributes['host'];
        $this->schemes = empty($attributes['scheme']) ? array() : (array)$attributes['scheme'];
        $this->middlewares = empty($attributes['middleware']) ? array() : (array)$attributes['middleware'];
        $this->attributes = $attributes; // pipe attributes
    }

    /**
     * Add route
     *
     * @param string         $name  route name
     * @param RouteInterface $route route object
     */
    public function add(string $name, RouteInterface $route)
    {
        $pipe = $this->getPipe();
        $route->setPipe($pipe);
        $this->routes[ltrim($pipe, '/').$name] = $route;
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
}
