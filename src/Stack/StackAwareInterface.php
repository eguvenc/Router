<?php

namespace Obullo\Router\Stack;

interface StackAwareInterface
{
    /**
     * Middleware add
     * 
     * @param  string $middleware middleware
     * @return self
     */
    public function middleware(string $middleware) : StackAwareInterface;

    /**
     * Returns to all middlewares
     * 
     * @return array
     */
    public function getStack() : array;
}
