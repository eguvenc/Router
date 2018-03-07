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
     * Add route
     * 
     * @param string         $name  route name
     * @param RouteInterface $route route object
     */
    public function add(string $name, RouteInterface $route);

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
     * Set host value
     * 
     * @param string $host host
     */
    public function setHost($host);

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