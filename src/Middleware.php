<?php

namespace Obullo\Router;

use SplQueue;

/**
 * Middleare
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Middleware
{
    protected $queue;

    public function __construct($middlewareClasses)
    {
        $this->queue = new SplQueue;
        $this->middlewareClasses = $middlewareClasses;
    }

    public function queue($name, $params = array())
    {
        $middleware = '\\' . trim($this->middlewareClasses, '\\') . '\\' . $name;
        if (! class_exists($middleware, false)) {
            $this->queue->enqueue(['callable' => new $middleware, 'params' => $params]);
        }
    }
}
