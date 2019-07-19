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
        $route = $this->collection->get($name);

        if (false === $route = $this->collection->get($name)) {
            throw new RouteNotFoundException(
                sprintf(
                    'The route "%s" is not available to generate the URL.',
                    htmlspecialchars($name)
                )
            );
        }
        $pattern = $route->getPattern();
        if (empty($params)) {
            return ($pattern == '/') ? '/' : rtrim($pattern, '/');
        }
        if (false == Self::isAssoc($params)) {
            throw new InvalidArgumentException('The url generator parameters must be key-value pairs.');
        }
        $types = $this->collection->getPatterns();
        $paramPattern = array();
        $paramReplace = array();
        foreach ($params as $key => $value) {
            if (! isset($types[$key])) {
                throw new UndefinedParameterException(
                    sprintf(
                        'The route "%s" parameter could not be resolved to generate the "%s" URL.',
                        htmlspecialchars($key),
                        htmlspecialchars($name)
                    )
                );
            }
            $paramPattern[] = '#\([^(]+\<'.preg_quote($key).'\>[^)]+\)#';
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