<?php

namespace Obullo\Router;

use Obullo\Router\Type;
use Obullo\Router\Exception\UndefinedTypeException;

/**
 * Pattern
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Pattern
{
    protected $types = array();
    protected $values = array();

    /**
     * Constructor
     * 
     * @param array $types multiple type array
     */
    public function __construct($types = array())
    {   
        foreach ((array)$types as $type) {
            $this->add($type);
        }
    }

    /**
     * Add type
     * 
     * @param Type $type object
     */
    public function add(Type $type)
    {
        $tag = $type->getTag();  // page
        $pattern = $type->getPattern(); // <int:page>

        $this->types[$tag] = $type;
        $this->values[$pattern] = $type->convert()->getValue();
    }
    
    /**
     * Returns to pattern types
     * 
     * @return array
     */
    public function getTypes() : array
    {
        return $this->types;
    }

    /**
     * Format pattern
     *
     * @param  string $unformatted string
     * @return string
     */
    public function format($unformatted)
    {
        return str_replace(
            array_keys($this->values),
            array_values($this->values),
            $unformatted
        );
    }

    /**
     * Validate route patterns
     *
     * @param  string $path route path
     * @return void
     */
    public function validateUnformattedPatterns(string $path)
    {
        foreach (explode('/', $path) as $value) {
            if ((substr($value, 0, 1) == '<' && substr($value, -1) == '>') && ! array_key_exists($value, $this->values)) {
                throw new UndefinedTypeException(
                    sprintf(
                        'The route type %s you used is undefined.',
                        htmlspecialchars($value)
                    )
                );
            }
        }
    }
}