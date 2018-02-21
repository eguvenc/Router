<?php

namespace Obullo\Router\Url;

use Obullo\Router\{
    Exception\RouteNotFoundException,
    Exception\ParameterLengthException,
    Exception\UndefinedParameterException
};
use InvalidArgumentException;

/**
 * Url Generator
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class UrlGenerator implements UrlGeneratorInterface
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
        // preg_match_all('#(<(.*?)>)#', 'welcome/(?<name>\w+)/(?<id>\d+)', $matches);

        $urlArray = explode('/', trim($pattern,'/'));
        $numberOfParams = 0;
        foreach ($urlArray as $value) {
            if (strpos($value, '(') !== false && strpos($value, ')') !== false) {
                ++$numberOfParams;
            }
        }
        if (count($params) != $numberOfParams) {
            throw new ParameterLengthException(
                sprintf(
                    'The expected number of parameters for the named "%s" route is "%d" but the number sent is "%d".',
                    $name,
                    $numberOfParams,
                    count($params)
                )
            );
        }
        $types = $this->collection->getTypes();
        $urlParams = '';
        foreach ($params as $name => $value) {
            if (! isset($types[$name])) {
                throw new UndefinedParameterException(
                    sprintf(
                        'The named "%s" parameter could not be resolved to generate the URL.',
                        $name
                    )
                );
            }
            array_pop($urlArray);
            $urlParams.= '/'.$types[$name]->toUrl($value);
        }
        return '/'.implode('/', $urlArray).$urlParams;
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