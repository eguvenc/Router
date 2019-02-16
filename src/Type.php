<?php

namespace Obullo\Router;

use Obullo\Router\Exception\BadTypeException;

/**
 * Abstract type
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
abstract class Type
{
    protected $tag;   // id, name
    protected $rule;  // <int:id>, <str:name>
    protected $value; // (?<any>.*) regex value
    protected $tags = array();

    /**
     * Contructor
     *
     * @param string $type  route type
     * @param string $regex regex rule
     */
    public function __construct(string $type, string $regex = null)
    {
        $this->type = $type;
        $type = rtrim(ltrim($type, '<'), '>');
        $this->tags = explode(':', $type);
        $this->tag 	= $this->tags[1];
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
     * Returns to type
     *
     * @return boolean
     */
    public function getType() : string
    {
        return $this->type;
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
    public function convert() : self
    {
        Self::validateType($this->getType(), $this->tags);
        $this->value = sprintf($this->getRegex(), $this->tag);
        return $this;
    }

    /**
     * Validate route type
     *
     * @param  string $type route type
     * @param  array  $tags exploded tags
     * @return void
     */
    protected static function validateType(string $type, array $tags)
    {
        if (strpos($type, '<') !== 0
            || substr($type, -1) != '>'
            || empty($tags[0])
            || empty($tags[1])
        ) {
            throw new BadTypeException(
                sprintf(
                    'The route type you entered must be in this format "%s".',
                    '<key:name>'
                )
            );
        }
    }
}
