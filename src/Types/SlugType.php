<?php

namespace Obullo\Router\Types;

use Obullo\Router\Type;

/**
 * Slug <slug:slug>
 *
 * @copyright Obullo
 * @license   https://opensource.org/licenses/BSD-3-Clause
 */
class SlugType extends Type
{
    protected $regex = '(?<%s>[a-zA-Z0-9_-]+)';

    /**
     * Allows to write to a cached static configuration file
     * 
     * @param  array  $array cached variables
     * @return object
     */
    public static function __set_state(array $array)
    {
        return new Self($array['pattern'], $array['regex']);
    }

    /**
     * Php format
     *
     * @param  number $value
     * @return int
     */
    public function toPhp($value)
    {
        return (string)$value;
    }

    /**
     * Url format
     *
     * @param mixed $value
     * @return string
     */
    public function toUrl($value)
    {
        return sprintf('%s', $value);
    }
}
