<?php

namespace Obullo\Router;

use Obullo\Router\Exception\BadTypeException;

/**
 * Abstract type
 *
 * @copyright Obullo
 * @license   https://opensource.org/licenses/BSD-3-Clause
 */
abstract class Type
{
    protected $tag;   // id, name
    protected $pattern;  // <int:id>, <str:name>
    protected $regex; // (?<any>.*) regex value with tag
    protected $value; // (?(.*).*) full of regex value
    protected $tags = array();

    /**
     * Contructor
     *
     * @param string $pattern route pattern
     * @param string $regex regex rule
     */
    public function __construct(string $pattern, string $regex = null)
    {
        $this->pattern = $pattern;
        $pattern = rtrim(ltrim($pattern, '<'), '>');
        $this->tags = explode(':', $pattern);
        $this->tag  = $this->tags[1];
        if (null != $regex) {
            $this->regex = $regex;
        }
    }

    /**
     * Php format
     *
     * @param  number $value
     * @return int
     */
    abstract public function toPhp($value);

    /**
     * Url format
     *
     * @param mixed $value
     * @return string
     */
    abstract public function toUrl($value);

    /**
     * Returns to pattern
     *
     * @return string
     */
    public function getPattern() : string
    {
        return $this->pattern;
    }

    /**
     * Returns to regex string of current pattern
     *
     * @return boolean
     */
    public function getRegex() : string
    {
        return $this->regex;
    }

    /**
     * Returns to regex value of current pattern
     *
     * @return string
     */
    public function getValue() : string
    {
        return $this->value;
    }

    /**
     * Returns to tag name of the rule
     * e.g. <str:name>, tag = name
     *
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Convert rule
     *
     * @return object
     */
    public function convert() : Self
    {
        Self::validatePattern($this->getPattern(), $this->tags);
        $this->value = sprintf($this->getRegex(), $this->tag);
        return $this;
    }

    /**
     * Validate route pattern
     *
     * @param  string $pattern
     * @param  array  $tags exploded tags
     * @return void
     */
    protected static function validatePattern(string $pattern, array $tags)
    {
        if (strpos($pattern, '<') !== 0
            || substr($pattern, -1) != '>'
            || empty($tags[0])
            || empty($tags[1])
        ) {
            throw new BadTypeException(
                sprintf(
                    'The pattern you entered must be in this format "%s".',
                    '<name:tag>'
                )
            );
        }
    }
}
