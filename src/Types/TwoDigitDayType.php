<?php

namespace Obullo\Router\Types;

use Obullo\Router\Type;

/**
 * Day <dd:day>
 *
 * @copyright Obullo
 * @license   https://opensource.org/licenses/BSD-3-Clause
 */
class TwoDigitDayType extends Type
{
    protected $regex = '(?<%s>[0-9]{2})';

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
        return sprintf('%02d', $value);
    }
}
