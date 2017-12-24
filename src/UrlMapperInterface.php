<?php

namespace Obullo\Router;

/**
 * UrlMapper Interface
 * 
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface UrlMapperInterface
{
    /**
     * Executer dispatch process
     * 
     * @return boolean
     */
    public function dispatch();

    /**
     * Returns to handler
     * 
     * @return mixed
     */
    public function getHandler();

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
}
