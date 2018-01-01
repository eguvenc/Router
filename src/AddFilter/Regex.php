<?php

namespace Obullo\Router\AddFilter;

use InvalidArgumentException;

/**
 * Regex filter
 *
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Regex implements FilterInterface
{
    protected $pattern;
    protected $match = false;

    /**
     * Constructor
     * 
     * @param string $pattern pattern
     */
    public function __construct($pattern)
    {
        if (! is_string($pattern)) {
            throw new InvalidArgumentException("Regex pattern must be string.");
        }
        $this->pattern = $pattern;
    }

    /**
     * Returns true if uri matched otherwise false
     * 
     * @param  string $path path
     * @return boolean
     */
    public function hasMatch($path)
    {
        if (preg_match('#^'.$this->pattern.'$#', $path)) {
            $this->match = true;
        }
        return $this->match;
    }
}
