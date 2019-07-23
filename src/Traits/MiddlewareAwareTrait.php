<?php

namespace Obullo\Router\Traits;

trait MiddlewareAwareTrait
{
    protected $middlewares = array();

    /**
     * Add middleware
     * 
     * @param array|string $middleware names
     */
    public function addMiddleware($middleware)
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
