<?php

namespace Obullo\Router;

/**
 * Dispatcher Interface
 * 
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface DispatcherInterface
{
    /**
     * Executer dispatch process
     * 
     * @return boolean
     */
    public function dispatch(UrlMapperInterface $mapper);

    /**
     * Returns to matched route methods
     * 
     * @return array
     */
    public function getMethods();

    /**
     * Get dispatched route arguments
     * 
     * @return array
     */
    public function getArgs();

    /**
     * Returns to handler
     * 
     * @return mixed
     */
    public function getHandler();
}
