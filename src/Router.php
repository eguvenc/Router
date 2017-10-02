<?php

namespace Obullo\Router;

use Obullo\Router\Group;
use InvalidArgumentException;
use Obullo\Router\Filter\FilterTrait;

/**
 * Router
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Router implements RouterInterface
{
    use AddTrait;
    use FilterTrait;

    protected $path;
    protected $method;
    protected $server;
    protected $group;
    protected $count = 0;
    protected $routes = array();
    protected $restful = false;  // default web router
    
    /**
     * Constructor
     *
     * @param string $path request uri path
     */
    public function __construct($path, $method, $server)
    {
        $this->path   = $path;
        $this->method = $method;
        $this->server = $server;
    }

    /**
     * Rewrite all http requests
     *
     * @param string $method  method
     * @param string $pattern regex pattern
     * @param string $rewrite replacement path
     *
     * @return void
     */
    public function rewrite($method, $pattern, $rewrite)
    {
        if (in_array($this->method, (array)$method)) {
            $pattern    = "/".ltrim($pattern, "/");
            $path       = preg_replace('#^'.$pattern.'$#', $rewrite, $this->path);
            $this->path = '/'.ltrim($path, '/');
        }
    }

    /**
     * Create a route
     *
     * @param string $method  method
     * @param string $pattern regex pattern
     * @param mixed  $handler mixed
     *
     * @return object
     */
    public function map($method, $pattern, $handler = null, $type = 'http')
    {
        ++$this->count;
        $this->routes[$this->count] = [
            'type' => $type,
            'method' => (array)$method,
            'pattern' => "/".ltrim($pattern, "/"),
            'handler' => $handler,
            'middlewares' => array()
        ];
        return $this;
    }

    /**
     * Create a secure request route
     *
     * @param string $method  method
     * @param string $pattern regex pattern
     * @param mixed  $handler mixed
     *
     * @return object
     */
    public function https($method, $pattern, $handler = null)
    {
        if ((!empty($this->server['HTTPS']) && $this->server['HTTPS'] !== 'off') || $this->server['SERVER_PORT'] == 443) {
            return $this->map($method, $pattern, $handler, __FUNCTION__);
        }
        return $this;
    }

    /**
     * Create a http xml request route
     *
     * @param string $method  method
     * @param string $pattern regex pattern
     * @param mixed  $handler mixed
     *
     * @return object
     */
    public function ajax($method, $pattern, $handler = null)
    {
        if (!empty($this->server['HTTP_X_REQUESTED_WITH']) && strtolower($this->server['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            return $this->map($method, $pattern, $handler, __FUNCTION__);
        }
        return $this;
    }

    /**
     * Create group
     *
     * @param string   $pattern  pattern
     * @param callable $callable callable
     *
     * @return object
     */
    public function group($pattern, $callable)
    {
        if (! is_callable($callable)) {
            throw new InvalidArgumentException("Group method second parameter must be callable.");
        }
        $this->group = ($this->group == null) ? new Group($this->path) : $this->group;
        $this->group->enqueue($pattern, $callable);
        return $this->group;
    }

    /**
     * Returns to routes
     *
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
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
    * Returns to group object
    *
    * @return object
    */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Sets restful routing functionality / Disable web routing
     *
     * @param boolean $bool on / off
     *
     * @return void
     */
    public function restful($bool = true)
    {
        $this->restful = $bool;
    }

    /**
     * Add middleware
     *
     * @param string $name middleware name
     * @param array  $args arguments
     *
     * @return void
     */
    protected function middleware($name, array $args)
    {
        $this->routes[$this->count]['middlewares'][] = array('name' => $name, 'params' => $args);
    }
}
