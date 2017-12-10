<?php

namespace Obullo\Router;

use Obullo\Middleware\Argument;

/**
 * Middleware Dispatcher
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class MiddlewareDispatcher extends Dispatcher
{
    protected $queue;

    /**
     * Execute dispatch process
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
        $this->queue  = $this->router->getQueue();

        foreach ($this->router->fetchRoutes() as $r) {

            if ($this->dispatch($r['pattern'])) {
                if (! in_array($this->request->getMethod(), (array)$r['method'])) {
                    $this->queue->enqueue('NotAllowed', new Argument((array)$r['method']));
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
     * Returns to middleware queue
     * 
     * @return object
     */
    public function getQueue()
    {
        return $this->queue;
    }

}