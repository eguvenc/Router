<?php

namespace Obullo\Router\Loader;

use Obullo\Router\RouteCollection;

/**
 * Loader interface
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface LoaderInterface
{
    /**
     * Load file
     * 
     * @param string $file file
     */
    public function load(string $file) : RouteCollection;
}