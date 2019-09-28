<?php

namespace Obullo\Router;

/**
 * Generator interface
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface GeneratorInterface
{
    /**
     * Generate url
     *
     * @param  string $name   path
     * @param  array  $args   arguments
     * @param  string $locale locale
     *
     * @return string|throw exception
     */
    public function generate(string $name, $args = array(), $locale = null);
}
