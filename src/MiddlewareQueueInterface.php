<?php

namespace Obullo\Router;

/**
 * Middleare Queue Interface
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface MiddlewareQueueInterface
{
    /**
     * Register path
     * 
     * @param string $path namespace
     * 
     * @return void
     */
    public function register($path)
    ;
    /**
     * Queue 
     * 
     * @param  string $name   class name
     * @param  array  $params parameters
     * 
     * @return void
     */
    public function queue($name, $params = array());

    /**
     * Returns to SplQueue
     * 
     * @return object
     */
    public function getQueue();

}
