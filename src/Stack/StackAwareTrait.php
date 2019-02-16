<?php

namespace Obullo\Router\Stack;

trait StackAwareTrait
{
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