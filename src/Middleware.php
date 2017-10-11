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
    protected $middlewarePath;

    /**
     * Constructor
     * 
     * @param SplQueue $queue object
     */
    public function __construct(SplQueue $queue)
    {
        $this->queue = $queue;
    }

    /**
     * Register path
     * 
     * @param string $path namespace
     * 
     * @return void
     */
    public function register($path)
    {
        $this->middlewarePath = $path;
    }

    /**
     * Queue 
     * 
     * @param  string $name   class name
     * @param  array  $params parameters
     * 
     * @return void
     */
    public function queue($name, $params = array())
    {
        $middleware = '\\' . trim($this->middlewarePath, '\\') . '\\' . $name;

        if (! class_exists($middleware, false)) {
            $this->queue->enqueue(['callable' => new $middleware, 'params' => $params]);
        }
    }
}
