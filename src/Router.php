<?php

namespace Obullo\Router;

use SplQueue;
use Obullo\Router\Group;
use InvalidArgumentException;
use Obullo\Middleware\Argument;
use Obullo\Router\AddFilter\FilterTrait;

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
    protected $queue;
    protected $server;
    protected $method;
    protected $request;
    protected $pattern;
    protected $response;
    protected $collection;
    protected $group = null;
    protected $match = false;
    protected $groupLevel = 0;
    protected $groupPath = "";
    protected $pathArray = array();
    protected $gPathArray = array();
    
    public function setUriPath($path)
    {
        $this->path = $path;
    }

    public function __construct($collection)
    {
        $this->collection = $collection;

        /*
        $this->request  = $request;
        $this->response = $response;
        */

        // $this->method = $request->getMethod();
        // $this->queue    = $queue;
        // $this->route    = new Route(new SplQueue);
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
     * Initialize router
     * 
     * @return void
     */
    public function init()
    {
        $this->pathArray = $this->gPathArray = explode("/", trim($this->path, "/"));
    }

    /**
     * GET
     * 
     * @param string $pattern rule
     * @param mixed  $handler handler
     * @return object
     */
    public function get($pattern, $handler = null)
    {
        return $this->map("GET", $pattern, $handler);
    }

    /**
     * POST
     * 
     * @param string $pattern rule
     * @param mixed  $handler handler
     * @return object
     */
    public function post($pattern, $handler = null)
    {
        return $this->map("POST", $pattern, $handler);
    }

    /**
     * PUT
     * 
     * @param string $pattern rule
     * @param mixed  $handler handler
     * @return object
     */
    public function put($pattern, $handler = null)
    {
        return $this->map("PUT", $pattern, $handler);
    }

    /**
     * PATCH
     * 
     * @param string $pattern rule
     * @param mixed  $handler handler
     * @return object
     */
    public function patch($pattern, $handler = null)
    {
        return $this->map("PATCH", $pattern, $handler);
    }

    /**
     * DELETE
     * 
     * @param string $pattern rule
     * @param mixed  $handler handler
     * @return object
     */
    public function delete($pattern, $handler = null)
    {
        return $this->map("DELETE", $pattern, $handler);
    }

    /**
     * OPTIONS
     * 
     * @param string $pattern rule
     * @param mixed  $handler handler
     * @return object
     */
    public function options($pattern, $handler = null)
    {
        return $this->map("OPTIONS", $pattern, $handler);
    }


    public function getCollection()
    {
        return $this->collection;        
    }

    /**
     * Route process
     * 
     * @return array|null
     */
    /*
    public function popRoute()
    {
        $queue = $this->collection->getRuleQueue();
        $route = $queue->dequeue();
        $path      = trim($this->path, "/");
        $pattern   = trim($route->getPattern(), "/");
        $regexRule = '#^'.$pattern.'$#';
        $args = array();
        if ($path == $pattern OR preg_match($regexRule, $path, $args)) {
            array_shift($args);
            $this->match = true;
            $this->pattern = $regexRule;
            $route->setArgs($args);
            return $route;
        }
        if (! $queue->isEmpty()) {
            $route = $this->popRoute();
            if (is_object($route)) {
                return $route;
            }
        }
        return null;
    }
    */


    /**
    * Returns to rewrited uri path
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
     * Returns to true if route match otherwise false
     * 
     * @return boolean
     */
    public function hasMatch()
    {
        return $this->match;
    }

    /**
     * Returns to matched pattern
     * 
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * Return to path array
     * 
     * @return array
     */
    public function getPathArray()
    {
        return $this->pathArray;
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
        $this->queue->enqueue($name, new Argument($args));
    }

}