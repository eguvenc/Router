<?php

namespace Obullo\Router;

/**
 * Dispatcher
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Dispatcher implements DispatcherInterface
{
    protected $router;
    protected $methods;
    protected $request;
    protected $handler;
    protected $response;
    protected $args = array();

    /**
     * Constructor
     * 
     * @param object $request  request
     * @param object $response response
     * @param object $router   router
     */
    public function __construct($request, $response, $router)
    {
        $this->router   = $router;
        $this->request  = $request;
        $this->response = $response;

        $this->router->init();
    }

    /**
     * Dsipatch request
     * 
     * @param  UrlMapperInterface $mapper object
     * @return mixed
     */
    public function dispatch(UrlMapperInterface $mapper)
    {
        $g = $this->router->popGroup();
        $r = $this->router->popRoute();
        if (! empty($r)) {
            $this->handler = $r['handler'];
            $this->methods = $r['method'];
            $this->args    = $r['args'];
            $mapper->execute($this);
        }
        if ($this->handler == null) {
            $this->handler = $g;
        }
        if (is_callable($this->handler)) {
            $callable = $this->handler;
            $this->handler = $callable($this->request, $this->response, $mapper);
        }
        if (is_string($this->handler)) {
            $this->handler = $mapper;
        }
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
     * Get dispatched route arguments
     * 
     * @return array
     */
    public function getArgs()
    {
        return $this->args;
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
}