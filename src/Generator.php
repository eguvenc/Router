<?php

namespace Obullo\Router;

use Obullo\Router\RouteCollection;
use Obullo\Router\Exception\RouteNotFoundException;
use Obullo\Router\Exception\UndefinedParameterException;
use InvalidArgumentException;

/**
 * Url Generator
 *
 * @copyright Obullo
 * @license   https://opensource.org/licenses/BSD-3-Clause
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
     * @param  string $name   path
     * @param  array  $args   arguments
     * @param  string $locale locale string (en,de,tr)
     *
     * @return string|throw exception
     */
    public function generate(string $name, $args = array(), $locale = null)
    {
        if ($name == '/') {
            return '/';
        }
        if (false === $route = $this->collection->get($name)) {
            throw new RouteNotFoundException(
                sprintf(
                    'The route "%s" is not available to generate the URL.',
                    htmlspecialchars($name)
                )
            );
        }
        $route->convert();
        $path = $route->getPath();
        echo $path;
        die;
        if ($locale != null) {
            $data = $this->collection->translatePath($name, $locale);
            $path = rtrim($data['path'], '/');
        }
        $urlParts  = explode('/', $path);
        $urlFormat = preg_replace('#<.*?>#', '%s', $path);
        
        if (strpos($urlFormat, '%s') > 0) {
            $urlFormat = vsprintf($urlFormat, $args);
        }
        $formattedValues = explode('/', $urlFormat);

        $types = $this->collection->getPattern()->getPatternTypes();
        $urlString = '';
        $i = 0;
        foreach ($urlParts as $part) {
            if (Self::isPattern($part)) {
                if (! isset($types[$part])) {
                    throw new UndefinedParameterException(
                        sprintf(
                            'The route "%s" parameter could not be resolved to generate the "%s" URL.',
                            htmlspecialchars($part),
                            htmlspecialchars($name)
                        )
                    );
                }
                $urlString.= $types[$part]->toUrl($formattedValues[$i]).'/';
            } else {
                $urlString.= $part.'/';
            }
            ++$i;
        }
        return rtrim($urlString, '/');
    }

    /**
     * Check url part is pattern
     *
     * @param  array $arr array
     * @return boolean
     */
    protected static function isPattern($part)
    {
        $first = substr($part, 0, 1);
        $last  = substr($part, -1);

        if ($first == '<' && $last == '>') {
            return true;
        }
        return false;
    }
}
