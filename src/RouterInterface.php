<?php

namespace Obullo\Router;

use Closure;

/**
 * Router Interface
 * 
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface RouterInterface
{
    /**
     * Rewrite all http requests
     * 
     * @param string $method  method
     * @param string $pattern regex pattern
     * @param string $rewrite replacement path
     * 
     * @return void
     */
    public function rewrite($method, $pattern, $rewrite);

    /**
     * Create group
     * 
     * @param string   $pattern  pattern
     * @param callable $callable callable
     * 
     * @return object
     */
    public function group($pattern, $callable);

    /**
     * Create a route
     * 
     * @param string $method  method
     * @param string $pattern regex pattern
     * @param mixed  $handler mixed
     * 
     * @return void
     */
    public function map($method, $pattern, $handler = null);
}
