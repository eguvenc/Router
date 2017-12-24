<?php

namespace Obullo\Router;

/**
 * UrlMapper
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class UrlMapper implements UrlMapperInterface
{
    protected $router;
    protected $handler;
    protected $methods = array();
    protected $args = array();

    /**
     * Constructor
     * 
     * @param object $router   router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
        $this->router->init();
    }

    /**
     * Dsipatch request
     * 
     * @return mixed
     */
    public function dispatch()
    {
        $g = $this->router->popGroup();
        $r = $this->router->popRoute();
        if (! empty($r)) {
            $this->handler = $r['handler'];
            $this->methods = $r['method'];
            $this->args    = $r['args'];
        }
        if ($this->handler == null) {
            $this->handler = $g;
        }
        return $this->handler;
    }

    /**
     * Returns to path array
     * 
     * @return array
     */
    public function getPathArray()
    {
        return $this->router->getPathArray();
    }

    /**
     * Returns to pattern
     * 
     * @return string
     */
    public function getPattern()
    {
        return $this->router->getPattern();
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
     * Returns to matched route methods
     * 
     * @return array
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * Get argument(s)
     * 
     * @param  string|null index $key string or number
     * @return mixed
     */
    public function getArgs($key = null)
    {
        if ($key === null) {
            return $this->args;
        }
        return isset($this->args[$key]) ? $this->args[$key] : false;
    }

    /**
     * Set arguments
     * 
     * @param array $args mapper arguments
     * @return object
     */
    public function setArgs($args)
    {
        $this->args = $args;
        return $this;
    }

   /**
     * Unset args
     * 
     * @return object
     */
    public function unsetArgs()
    {
        $this->args = array();
        return $this;
    }

}