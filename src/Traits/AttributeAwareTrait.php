<?php

namespace Obullo\Router\Traits;

/**
 * AttributeAwareTrait
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
trait AttributeAwareTrait
{
    protected $attributes;

    /**
     * Set pipe attribute
     *
     * @param string $key   string
     * @param mixed  $value value
     */
    public function setAttribute(string $key, $value)
    {
        $this->attributes['$'.$key] = $value;
    }

    /**
     * Returns to pipe attribute
     *
     * @param  string $key name
     * @return mixed value
     */
    public function getAttribute(string $key)
    {
        return isset($this->attributes['$'.$key]) ? $this->attributes['$'.$key] : null;
    }
}