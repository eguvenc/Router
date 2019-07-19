<?php

namespace Obullo\Router;

use Obullo\Router\RequestContext;
use Obullo\Router\Traits\RequestContextAwareTrait;
use Obullo\Router\Exception\BadRouteException;
use Obullo\Router\Exception\UndefinedTypeException;
use Obullo\Router\Exception\RouteConfigurationException;
use ArrayIterator;
use IteratorAggregate;
use Countable;

/**
 * Route collection
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class RouteCollection implements IteratorAggregate, Countable
{
    use RequestContextAwareTrait;

    protected $rules = array();
    protected $patterns = array();
    protected $routes = array();

    /**
     * Constructor
     *
     * @param ArrayAccess $config config
     */
    public function __construct(array $config)
    {
        if (! isset($config['patterns'])) {
            throw new RouteConfigurationException(
                'Please provide route patterns to create the route collection.'
            );
        }
        foreach ($config['patterns'] as $object) {
            $type = $object->getType();
            $tag  = $object->getTag();
            $this->rules[$type] = $object->convert()->getValue();
            $this->patterns[$tag] = $object;
        }
    }

    /**
     * Add route
     *
     * @param string         $path  route path
     * @param RouteInterface $route object
     */
    public function add(string $path, RouteInterface $route)
    {
        $route->setName($path);
        $unformatted = $route->getPattern();
        $this->validateUnformattedPattern($unformatted);
        $formatted = $this->formatPattern($unformatted);
        $host = $this->formatPattern($route->getHost());
        $route->setHost($host);
        $route->setPattern($formatted);
        $this->routes[$path] = $route;
    }

    /**
     * Returns to number of routes
     *
     * @return int
     */
    public function count() : int
    {
        return count($this->routes);
    }

    /**
     * Returns to all routes
     *
     * @return array
     */
    public function all() : array
    {
        return $this->routes;
    }

    /**
     * Gets the current RouteCollection as an Iterator that includes all routes.
     *
     * It implements IteratorAggregate.
     *
     * @return ArrayIterator object
     */
    public function getIterator()
    {
        return new ArrayIterator($this->routes);
    }

    /**
     * Returns to patterns
     *
     * @return array
     */
    public function getPatterns()
    {
        return $this->patterns;
    }

    /**
     * Returns to selected route
     *
     * @param  string $name name
     * @return boolean
     */
    public function get(string $path)
    {
        return isset($this->routes[$path]) ? $this->routes[$path] : false;
    }

    /**
     * Remove route
     *
     * @param  string $name name
     * @return void
     */
    public function remove(string $path)
    {
        unset($this->routes[$path]);
    }

    /**
     * Format pattern
     *
     * @param  string $unformatted string
     * @return string
     */
    public function formatPattern($unformatted)
    {
        return str_replace(
            array_keys($this->rules),
            array_values($this->rules),
            $unformatted
        );
    }

    /**
     * Validate route patterns
     *
     * @param  string $pattern patterns
     * @return void
     */
    protected function validateUnformattedPattern(string $pattern)
    {
        foreach (explode('/', $pattern) as $value) {
            if ((substr($value, 0, 1) == '<' && substr($value, -1) == '>') && ! array_key_exists($value, $this->rules)) {
                throw new UndefinedTypeException(
                    sprintf(
                        'The route type %s you used is undefined.',
                        $value
                    )
                );
            }
        }
    }
}
