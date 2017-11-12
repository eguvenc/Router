<?php

namespace Obullo\Router\Filter;

use InvalidArgumentException;

/**
 * Middleware conditions
 *
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Condition
{
    protected $path;
    protected $match = false;

    /**
     * Contsructor
     *
     * @param string $path uri path
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * If uri contains path(s)
     *
     * @param strig|array $path path
     *
     * @return object
     */
    public function contains($path)
    {
        $result = "";
        foreach ((array)$path as $value) {
            $result.= stripos($this->path, "/".trim($value, "/"));
        }
        if (! empty($result)) {
            $this->match = true;
        }
        return $this;
    }

    /**
     * If uri NOT contains path(s)
     *
     * @param strig|array $path path
     *
     * @return object
     */
    public function notContains($path)
    {
        $result = "";
        foreach ((array)$path as $value) {
            $result.= stripos($this->path, "/".trim($value, "/"));
        }
        if (empty($result)) {
            $this->match = true;
        }
        return $this;
    }

    /**
     * If uri match with regex
     *
     * @param string $pattern pattern
     *
     * @return void
     */
    public function regex($pattern)
    {
        if (! is_string($pattern)) {
            throw new InvalidArgumentException("Regex pattern must be string.");
        }
        if (preg_match('#^'.$pattern.'$#', $this->path)) {
            $this->match = true;
        }
        return $this;
    }

    /**
     *  If uri NOT match with regex
     *
     * @param string $pattern pattern
     *
     * @return void
     */
    public function notRegex($pattern)
    {
        if (! is_string($pattern)) {
            throw new InvalidArgumentException("Regex pattern must be string.");
        }
        if (! preg_match('#^'.$pattern.'$#', $this->path)) {
            $this->match = true;
        }
        return $this;
    }

    /**
     * Returns to condition match result
     *
     * @return boolean
     */
    public function hasMatch()
    {
        return $this->match;
    }
}
