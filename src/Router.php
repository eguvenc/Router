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
    protected $groupLevel = 0;
    protected $segments = array();
    protected $count = 0;
    protected $routes = array();
    protected $restful = false;  // default web router
    protected $middlewareQueue;
    
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
     * Initialize router start methods
     * 
     * @return void
     */
    public function init()
    {
        $this->segments = explode("/", trim($this->path, "/"));
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
        $prefix = '';
        $rule = trim($pattern, "/");
        if ($this->groupLevel > 0) {
            preg_match("#(.*?)".$rule."#", $this->getPath(), $output); // Add root uri path for current group rule
            $prefix = (isset($output[1])) ? $output[1] : '';
        }
        ++$this->count;
        $this->routes[$this->count] = [
            'method' => (array)$method,
            'pattern' => $prefix.$rule,
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
        $g      = $this->group->dequeue();
        $folder = trim($g['pattern'], "/");

        if (! empty($this->segments[0]) && $this->segments[0] == $folder) { // Execute the group if segment equal to group name.
            ++$this->groupLevel;
            $handler = $g['callable']($this->request, $this->response);
            if ($this->middlewareQueue != null && ! empty($g['middlewares'])) {
                foreach ((array)$g['middlewares'] as $value) {
                    $this->middlewareQueue->queue($value['name'], $value['params']);
                }
            }
            array_shift($this->segments); // Remove first segment from the path array
        }

        if (! $this->group->isEmpty()) {
            $handler = $this->popGroup();
        }
        $this->groupLevel = 0;
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
     * Set middleware
     * 
     * @param object $middleware middleware queue
     */
    public function setMiddlewareQueue($middlewareQueue)
    {
        $this->middlewareQueue = $middlewareQueue;
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
