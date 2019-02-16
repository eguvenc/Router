<?php

namespace Obullo\Router;

use Obullo\Router\Stack\StackAwareTrait;
use Obullo\Router\Stack\StackAwareInterface;

/**
 * Route
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Route implements StackAwareInterface, RouteInterface
{
    use StackAwareTrait;

    protected $name;
    protected $host;
    protected $pattern;
    protected $route = array();
    protected $methods = array();
    protected $middlewares = array();
    protected $handler = null;
    protected $arguments = array();
    protected $schemes = array();

    /**
    * Create a new route
    *
    * @param string|array $route attributes
    * @return object
    */
    public function __construct(array $route)
    {
        $this->route = $route;
        $method = isset($route['method']) ? $route['method'] : 'GET';
        foreach ((array)$method as $name) {
            $this->methods[] = strtoupper($name);
        }
        $this->handler = $route['handler'];
        $this->pattern = '/'.ltrim($route['path'], '/');
        $this->middlewares = empty($route['middleware']) ? array() : (array)$route['middleware'];
        $this->host = empty($route['host']) ? null : $route['host'];
        $this->schemes = empty($route['scheme']) ? array() : (array)$route['scheme'];
    }

    /**
     * Set pipe
     *
     * @param string $pipe pipe
     * @return object
     */
    public function setPipe(string $pipe)
    {
        $this->pattern = '/'.ltrim($pipe, '/').ltrim($this->pattern, '/');
    }

    /**
     * Set route name
     *
     * @param string $name name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns to path name
     *
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * Returns to route methods
     *
     * @return array
     */
    public function getMethods() : array
    {
        return $this->methods;
    }

    /**
     * Returns to handler
     *
     * @return mixed
     */
    public function getHandler()
    {
        return $this->handler;
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
     * Set schemes
     *
     * @param array $schemes schemes
     */
    public function setSchemes($schemes)
    {
        $this->schemes = (array)$schemes;
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
     * Set arguments
     *
     * @param array $arguments matched argumets
     */
    public function setArguments(array $arguments)
    {
        $this->arguments = $arguments;
    }

    /**
     * Remove argument
     *
     * @param  string $key name
     * @return void
     */
    public function removeArgument(string $key)
    {
        unset($this->arguments[$key]);
    }

    /**
     * Get argument
     *
     * @param  string index $key string
     * @return mixed
     */
    public function getArgument(string $key)
    {
        return isset($this->arguments[$key]) ? $this->arguments[$key] : false;
    }

    /**
     * Get argument(s)
     *
     * @param  string|null index $key string or number
     * @return mixed
     */
    public function getArguments() : array
    {
        return $this->arguments;
    }

    /**
     * Set route attribute
     *
     * @param string $key   string
     * @param mixed  $value value
     */
    public function setAttribute(string $key, $value)
    {
        $this->route[$key] = $value;
    }

    /**
     * Returns to route attribute
     *
     * @param  string $key name
     * @return mixed
     */
    public function getAttribute(string $key)
    {
        return isset($this->route[$key]) ? $this->route[$key] : null;
    }

    /**
     * Set pattern
     *
     * @param string $pattern
     */
    public function setPattern(string $pattern)
    {
        $this->pattern = (string)$pattern;
    }

    /**
     * Returns to pattern
     *
     * @return string
     */
    public function getPattern() : string
    {
        return $this->pattern;
    }
}
