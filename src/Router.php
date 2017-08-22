<?php

namespace Obullo\Router;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use SplQueue;
use Obullo\Router\Group;
use InvalidArgumentException;
use Obullo\Router\Filter\FilterTrait;
use Interop\Container\ContainerInterface as Container;

/**
 * Router
 *
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Router implements RouterInterface
{
    use AddTrait;
    use FilterTrait;

    protected $path;
    protected $group;
    protected $class;
    protected $queue;
    protected $folder;
    protected $method;
    protected $handler;
    protected $request;
    protected $response;
    protected $container;
    protected $count = 0;
    protected $routes = array();
    protected $params = array();
    protected $webRouting = true;  // default web router
    protected $dispatched = false;
    
    /**
     * Construct
     *
     * @param Request  $request  request
     * @param Response $response response
     */
    public function __construct(Request $request, Response $response)
    {
        $this->path      = $request->getUri()->getPath();
        $this->request   = $request;
        $this->response  = $response;
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
        if (in_array($this->request->getMethod(), (array)$method)) {
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
     * @return void
     */
    public function map($method, $pattern, $handler = null)
    {
        ++$this->count;
        $this->routes[$this->count] = [
            'method' => (array)$method,
            'pattern' => "/".ltrim($pattern, "/"),
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
        $this->group = ($this->group == null) ? new Group($this->request) : $this->group;
        $this->group->enqueue($pattern, $callable);
        return $this->group;
    }

    /**
     * Route process
     *
     * @return void
     */
    protected function dispatch()
    {
        $args = array();
        $this->dispatched = false;
        foreach ($this->routes as $r) {
            $handler = $r['handler'];
            $pattern = $r['pattern'];
            if (trim($pattern, "/") == trim($this->path, "/") || preg_match('#^'.$pattern.'$#', $this->path, $args)) {
                if (! in_array($this->request->getMethod(), (array)$r['method'])) {
                    $notAllowed = '\\'. APP_NAME .'\Middleware\NotAllowed';
                    $this->queue->enqueue(['callable' => new $notAllowed, 'params' => (array)$r['method']]);
                    continue;
                }
                $this->queue($r['middlewares']);

                array_shift($args);
                $this->params  = $args;
                $this->request = $this->request->withAttribute('args', $args);

                if (is_string($handler)) {
                    $this->handler = $handler;
                    $this->dispatched = true;
                }
                if (is_callable($handler)) {
                    $this->handler = $handler($this->request, $this->response);
                    $this->dispatched = true;
                }
            }
        }
        $this->setDefaultHandler();  // Auto resolve if route not exists and web routing is on.
    }

    /**
     * Returns to router request object
     *
     * @return object
     */
    public function getRequest()
    {
        return $this->request;
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
        $this->webRouting = ($bool) ? false : true;
    }

    /**
     * Set default path as handler ( Resolves current path if has no route match )
     *
     * @return void
     */
    protected function setDefaultHandler()
    {
        if ($this->handler == null && $this->webRouting) {
            $this->dispatched = true;
            $this->handler = $this->path;
        }
    }

    /**
     * Group process
     *
     * @return void
     */
    public function popGroup()
    {
        if ($this->group == null) {
            return;
        }
        $exp   = explode("/", trim($this->path, "/"));
        $group = $this->group->dequeue();

        if (in_array(trim($group['pattern'], "/"), $exp, true)) {
            $group['callable']($this->request, $this->response);
            $this->queue($group['middlewares']);
        }
        if (! $this->group->isEmpty()) {
            $this->popGroup();
        }
    }

    /**
     * Get executed handler result
     *
     * @return object|string
     */
    public function getHandler()
    {
        if (! $this->dispatched) {  // Run one time, this function runs twice
            $this->popGroup();      // in App.php invoke() method.
            $this->dispatch();
        }
        return $this->handler;
    }

    /**
     * Returns to uri parameters
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Set queue for middlewares
     *
     * @param SplQueue $queue queue
     *
     * @return void
     */
    public function setQueue(SplQueue $queue)
    {
        $this->queue = $queue;
    }

    /**
     * Return queue for middlewares
     *
     * @param SplQueue $queue queue
     *
     * @return void
     */
    public function getQueue()
    {
        return $this->queue;
    }

    /**
     * Queue middlewares
     *
     * @param array $middlewares middlewares
     *
     * @return void
     */
    protected function queue($middlewares)
    {
        if (empty($middlewares)) {
            return;
        }
        foreach ((array)$middlewares as $value) {
            $middleware = '\\'. APP_NAME .'\Middleware\\'.$value['name'];
            if (! class_exists($middleware, false)) {
                $this->queue->enqueue(['callable' => new $middleware, 'params' => $value['params']]);
            }
        }
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

    /**
     * Set the class name
     *
     * @param string $class classname segment 1
     *
     * @return object Router
     */
    public function setClass($class)
    {
        $this->class = $class;
        return $this;
    }

    /**
     * Set current method
     *
     * @param string $method name
     *
     * @return object Router
     */
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    /**
     * Set the folder name
     *
     * @param string $folder folder
     *
     * @return object Router
     */
    public function setFolder($folder)
    {
        $this->folder = $folder;
        return $this;
    }

    /**
     * Get folder, folder name first letter must be uppercase
     *
     * @param string $separator get folder seperator
     *
     * @return string
     */
    public function getFolder($separator = '')
    {
        return (empty($this->folder)) ? '' : ucfirst($this->folder).$separator;
    }

    /**
     * Returns to current routed class name
     *
     * @return string
     */
    public function getClass()
    {
        return ucfirst($this->class);  // class name first letter must be uppercase
    }

    /**
     * Returns to current method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Returns php namespace of the current route
     *
     * @return string
     */
    public function getNamespace()
    {
        $folder = $this->getFolder();
        if (strpos($folder, "/") > 0) {  // Converts "Tests\Welcome/home" to Tests\Welcome\Home
            $exp = explode("/", $folder);
            $folder = trim(implode("\\", $exp), "\\");
        }
        $namespace = $folder;
        $namespace = trim($namespace, '\\');
        return (empty($namespace)) ? '' : $namespace.'\\';
    }

    /**
     * Clean dispatcher variables
     *
     * @return void
     */
    public function clear()
    {
        $this->class  = '';
        $this->folder = '';
    }
}
