<?php

namespace Obullo\Router\Stack;

trait StackAwareTrait
{
    /**
     * Middleware add
     * 
     * @param  string $middleware middleware
     * @return object StackInterface
     */
    public function middleware(string $middleware) : StackAwareInterface
    {
        $this->middlewares[] = $middleware;
        return $this;   
    }

    /**
     * Returns to all middlewares
     * 
     * @return array
     */
    public function getStack() : array
    {
        return $this->middlewares;
    }
}