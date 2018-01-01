<?php

namespace Obullo\Router\AddFilter;

/**
 * Filter Interface
 * 
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface FilterInterface
{
    /**
     * Returns true if uri matched otherwise false
     * 
     * @param  string $path path
     * @return boolean
     */
    public function hasMatch($path);
}