<?php

namespace Obullo\Router;

use Obullo\Router\Pattern;
use Obullo\Router\Traits\MiddlewareAwareTrait;

/**
 * Route
 *
 * @copyright Obullo
 * @license   https://opensource.org/licenses/BSD-3-Clause
 */
class Route implements RouteInterface
{
    use MiddlewareAwareTrait;
    
    protected $path;
    protected $name;
    protected $host;
    protected $pattern;
    protected $route = array();
    protected $methods = array();
    protected $handler = null;
    protected $arguments = array();
    protected $schemes = array();

    /**
     * Consructor
     *
     * @param mixed  $method  http method name
     * @param string $path    route path
     * @param string $handler route handler
     * @param string $host    http host
     * @param mixed  $scheme  url scheme
     * @param mixed  $middleware middlewares
     */
    public function __construct($method, string $path, string $handler, $host = null, $scheme = null, $middleware = array())
    {
        foreach ((array)$method as $name) {
            $this->methods[] = strtoupper($name);
        }
        $this->setPath($path);
        $this->setHandler($handler);
        $this->setHost($host);
        $this->setSchemes($scheme);
        $this->middleware($middleware);
    }

    /**
     * Set pattern
     *
     * @param Pattern $pattern pattern
     */
    public function setPattern(Pattern $pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * Convert route variables
     *
     * @return void
     */
    public function convert()
    {
        /**
         * Check some validations for unformatted tags
         */
        $this->pattern->validateUnformattedPatterns($this->getPath());

        $path = $this->pattern->format($this->getPath());
        $host = $this->pattern->format($this->getHost());

        $this->setPath($path);
        $this->setHost($host);
    }

    /**
     * Set name
     *
     * @param string $name name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * Returns to name
     *
     * @param string $path path
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * Set path
     *
     * @param string $path path
     */
    public function setPath(string $path)
    {
        $this->path = ($path == '/') ? '/' : '/'.trim($path, '/').'/'; // normalize route rules
    }

    /**
     * Returns to path label
     *
     * @return string
     */
    public function getPath() : string
    {
        return $this->path;
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
     * Set handler
     *
     * @param string $handler handler
     */
    public function setHandler(string $handler)
    {
        $this->handler = $handler;
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
}
