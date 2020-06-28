<?php

namespace Obullo\Router\Types;

use Obullo\Router\Type;

/**
 * Any <any:any>
 *
 * @copyright Obullo
 * @license   https://opensource.org/licenses/BSD-3-Clause
 */
class AnyType extends Type
{
    protected $regex = '(?<%s>.*)';

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
