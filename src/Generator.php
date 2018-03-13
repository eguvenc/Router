<?php

namespace Obullo\Router;

use Obullo\Router\{
    RouteCollection,
    Exception\BadParameterException,
    Exception\RouteNotFoundException,
    Exception\UndefinedParameterException
};
use InvalidArgumentException;

/**
 * Url Generator
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Generator implements GeneratorInterface
{
    protected $collection;

    /**
     * Constructor
     * 
     * @param RouteCollection $collection routes
     */
    public function __construct(RouteCollection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * Generate url
     * 
     * @param  string $name   route name
     * @param  array  $params url parameters
     * @return string
     */
    public function generate(string $name, $params = array())
    {
        if (false === $route = $this->collection->get($name)) {
            throw new RouteNotFoundException(
                sprintf(
                    'The named "%s" route is not available to generate the URL.',
                    $name
                )
            );
        }
        $pattern = $route->getPattern();
        if (empty($params)) {
            return rtrim($pattern, '/');
        }
        if (false == Self::isAssoc($params)) {
            throw new InvalidArgumentException('The url generator parameters must be key-value pairs.');
        }
        $types = $this->collection->getTypes();
        $paramPattern = array();
        $paramReplace = array();
        foreach ($params as $key => $value) {
            if (! isset($types[$key])) {
                throw new UndefinedParameterException(
                    sprintf(
                        'The named "%s" parameter could not be resolved to generate the "%s" URL.',
                        $key,
                        $name
                    )
                );
            }
            $paramPattern[] = '#\([^(]+\<'.$key.'\>[^)]+\)#';
            $paramReplace[] = $types[$key]->toUrl($value);
        }
        $urlString = preg_replace($paramPattern, $paramReplace, $pattern);
        if (strpos($urlString, '(') !== false) {
            throw new BadParameterException(
                sprintf(
                    'Some parameters could not be resolved for the "%s" URL.',
                    $urlString
                )
            );
        }
        return rtrim($urlString, '/');
    }

    /**
     * Check array is associative
     * 
     * @param  array $arr array
     * @return boolean
     */
    protected static function isAssoc(array $arr)
    {
        if (array() === $arr) {
            return false;
        }
        return array_keys($arr) !== range(0, count($arr) - 1);
    }
}