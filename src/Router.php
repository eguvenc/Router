<?php

namespace Obullo\Router;

use SplQueue;
use Obullo\Router\Group;
use InvalidArgumentException;
use Obullo\Middleware\Argument;
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
    protected $queue;
    protected $server;
    protected $method;
    protected $request;
    protected $pattern;
    protected $response;
    protected $group = null;
    protected $match = false;
    protected $groupLevel = 0;
    protected $groupPath  = "";
    protected $segments = array();
    
    /**
     * Constructor
     * 
     * @param object $request  request
     * @param obejct $response response
     * @param obejct $queue    middleware
     */
    public function __construct($request, $response, $queue = null)
    {
        $this->request  = $request;
        $this->response = $response;
        $this->path     = $request->getUri()->getPath();
        $this->method   = $request->getMethod();
        $this->server   = $request->getServerParams();
        $this->queue    = $queue;
        $this->route    = new Route(new SplQueue);
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
           $prefix = $this->groupPath;
        }
        $payload = [
            'method' => (array)$method,
            'pattern' => $prefix.$rule,
            'handler' => $handler
        ];
        $this->route->enqueue($payload);
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
        $this->group = ($this->group == null) ? new Group($this->queue) : $this->group;
        $this->group->enqueue(['pattern' => $pattern,'callable' => $callable]);
        return $this->group;
    }

    /**
     * Route process
     * 
     * @return array|null
     */
    public function popRoute()
    {
        $r = $this->route->dequeue();
        $path      = trim($this->path, "/");
        $pattern   = trim($r['pattern'], "/");
        $regexRule = '#^'.$pattern.'$#';
        $args = array();
        if ($path == $pattern OR preg_match($regexRule, $path, $args)) {
            array_shift($args);
            $this->match = true;
            $this->pattern = $regexRule;
            $r['args'] = $args;
            return $r;
        }
        if (! $this->route->isEmpty()) {
            $r = $this->popRoute();
            if (is_array($r)) {
                return $r;
            }
        }
        return null;
    }

    /**
     * Group process
     * 
     * @return mixed|null
     */
    public function popGroup()
    {
        $args = array();
        $handler = null;
        if ($this->group == null) {
            return;
        }
        $g = $this->group->dequeue();
        $folder = trim($g['pattern'], "/");
        
        if (! empty($this->segments[0]) && $this->segments[0] == $folder) { // Execute the group if segment equal to group name.
            ++$this->groupLevel;
            $this->groupPath.= $folder."/";
            $handler = $g['callable']($this->request, $this->response);
            array_shift($this->segments); // Remove first segment from the path array
        }
        if (! $this->group->isEmpty()) {
            $handler = $this->popGroup();
        }
        $this->groupLevel = 0;
        return $handler;
    }

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
     * Returns to middleware queue
     * 
     * @return object
     */
    public function getQueue()
    {
        return $this->queue;
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
     * Return to uri segments
     * 
     * @return array
     */
    public function getSegments()
    {
        return $this->segments;
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