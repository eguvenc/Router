<?php

namespace Obullo\Router\Stack;

interface StackAwareInterface
{
    /**
     * Returns to all middlewares
     * 
     * @return array
     */
    public function getStack() : array;
}