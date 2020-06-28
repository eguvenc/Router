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
