<?php

namespace Obullo\Router;

/**
 * Pipe interface
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface PipeInterface
{
    /**
     * Returns to pipe
     * 
     * @return string
     */
    public function getPipe() : string;

    /**
     * Returns to matched routes
     * 
     * @param  string $path path
     * @return array|false
     */
    public function match(string $path);
}