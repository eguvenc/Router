<?php

namespace Obullo\Router\Types;

use Obullo\Router\Type;

/**
 * Integer <int:name>
 *
 * @copyright Obullo
 * @license   https://opensource.org/licenses/BSD-3-Clause
 */
class IntType extends Type
{
    protected $regex = '(?<%s>\d+)';

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
        return (int)$value;
    }

    /**
     * Url format
     *
     * @param mixed $value
     * @return string
     */
    public function toUrl($value)
    {
        return sprintf('%d', $value);
    }
}
