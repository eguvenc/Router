<?php

namespace Obullo\Router\Traits;

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
