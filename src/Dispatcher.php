<?php

namespace Obullo\Router;

/**
 * Dispatcher
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Dispatcher
{
    protected $path;
    protected $router;
    protected $request;
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
        $this->router     = $router;
        $this->request    = $request;
        $this->response   = $response;
        $this->path       = $router->getPath();
    }

    /**
     * Dispatch route
     * 
     * @param array $pattern pattern
     * 
     * @return boolean
     */
    public function dispatch($pattern)
    {
        $args = array();
        if (trim($pattern, "/") == trim($this->path, "/") ||
            preg_match('#^'."/".ltrim($pattern, "/").'$#', $this->path, $args)
        ) {
            array_shift($args);
            $this->args = $args;
            return true;
        }
        return false;
    }

    /**
     * Executer dispatch process
     *
     * @param mixed $middlewareQueue is optional
     * 
     * @return mixed handler
     */
    public function execute($middlewareQueue = null)
    {
        $this->router->setMiddlewareQueue($middlewareQueue);
        $this->router->init();

        $handler = null;
        $groupHandler = $this->router->popGroup();

        foreach ($this->router->fetchRoutes() as $r) {

            if ($this->dispatch($r['pattern'])) {
                if (! in_array($this->request->getMethod(), (array)$r['method'])) {
                    $middlewareQueue->queue('NotAllowed', (array)$r['method']);
                    continue; // stop process
                }
                if (! empty($r['middlewares'])) {
                    foreach ((array)$r['middlewares'] as $value) {
                        $middlewareQueue->queue($value['name'], $value['params']);
                    }
                }
                if (is_string($r['handler'])){
                    $handler = $r['handler'];
                }
                if (is_callable($r['handler'])) {
                    $handler = $r['handler']($this->request, $this->response, $this->getArgs());
                }
            }
        }
        if ($handler == null) {
            $handler = $groupHandler;
        }
        return $handler;
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
}