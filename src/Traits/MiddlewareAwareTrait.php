<?php

namespace Obullo\Router\Traits;

/**
 * MiddlewareAwareTrait
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
trait MiddlewareAwareTrait
{
    protected $middlewares = array();

    /**
     * Add middleware
     *
     * @param array|string $middleware names
     */
    public function middleware($middleware)
    {
        foreach ((array)$middleware as $class) {
            $this->middlewares[] = $class;
        }
    }

    /**
     * Returns to all middlewares
     *
     * @return array
     */
    public function getMiddlewares() : array
    {
        return $this->middlewares;
    }
}
