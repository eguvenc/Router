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
        $rule = '#^'."/".ltrim($pattern, "/").'$#';
        $args = array();
        if (trim($pattern, "/") == trim($this->path, "/") ||
            preg_match($rule, $this->path, $args)
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
     * @param mixed $queue middleware is optional
     * 
     * @return mixed handler
     */
    public function execute()
    {
        $this->router->init();

        $handler = null;
        $groupHandler = $this->router->popGroup();

        foreach ($this->router->fetchRoutes() as $r) {
            if ($this->dispatch($r['pattern'])) {
                if (! in_array($this->request->getMethod(), (array)$r['method'])) {
                    continue; // stop process
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