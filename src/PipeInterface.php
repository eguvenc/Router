<?php

namespace Obullo\Router;

/**
 * Pipe interface
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface PipeInterface
{
    /**
     * Returns to pipe
     * 
     * @return string
     */
    public function getPipe() : string;

    /**
     * Returns to routes
     *
     * @return array
     */
    public function getRoutes() : array;

    /**
     * Returns to host
     * 
     * @return null|string
     */
    public function getHost();

    /**
     * Returns to schemes
     * 
     * @return array
     */
    public function getSchemes() : array;
}