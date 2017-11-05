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
    protected $request;
    protected $response;
    protected $server;
    protected $group = null;
    protected $groupPaths = array();
    protected $count = 0;
    protected $routes = array();
    protected $restful = false;  // default web router
    protected $middleware;
    
    /**
     * Constructor
     * 
     * @param object $request  request
     * @param obejct $response response
     */
    public function __construct($request, $response)
    {
        $this->request  = $request;
        $this->response = $response;
        $this->path     = $request->getUri()->getPath();
        $this->method   = $request->getMethod();
        $this->server   = $request->getServerParams();
    }


    public function setMiddleware($middleware)
    {
        $this->middleware = $middleware;
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
     *
     * @return object
     */
    public function map($method, $pattern, $handler = null)
    {
        ksort($this->groupPaths);
        $groupPattern = empty($this->groupPaths) ? "/" : "/".implode($this->groupPaths, "/")."/";

        ++$this->count;
        $this->routes[$this->count] = [
            'method' => (array)$method,
            'pattern' => $groupPattern.trim($pattern, "/"),
            'handler' => $handler,
            'middlewares' => array()
        ];
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
     * Group process
     * 
     * @return void
     */
    public function popGroup()
    {
        $args = array();
        $handler = null;
        if ($this->group == null) {
            return;
        }
        $exp    = explode("/", trim($this->path, "/"));
        $g      = $this->group->dequeue();
        $folder = trim($g['pattern'], "/");

        $i = 0;
        foreach ($exp as $item) {
            if ($item == $folder) {
                $this->groupPaths[$i] = $item;
                $handler = $g['callable']($this->request, $this->response);
            }
            ++$i;
        }
        if ($this->middleware != null && ! empty($g['middlewares'])) {
            foreach ((array)$g['middlewares'] as $value) {
                $this->middleware->queue($value['name'], $value['params']);
            }
        }
        if (! $this->group->isEmpty()) {
            $handler = $this->popGroup();
        }
        $this->groupPaths = array();
        return $handler;
    }

    /**
     * Returns to routes
     *
     * @return array
     */
    public function fetchRoutes()
    {
        return $this->routes;
    }

    /**
    * Returns to rewrited path
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
