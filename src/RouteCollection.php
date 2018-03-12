<?php

namespace Obullo\Router;

use Obullo\Router\{
    RequestContext,
    Exception\BadRouteException,
    Exception\UndefinedTypeException,
    Traits\RequestContextAwareTrait
};
use ArrayAccess;
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
    protected $types = array();
    protected $pipes = array();
    protected $routes = array();

    /**
     * Constructor
     * 
     * @param ArrayAccess $config config
     */
	public function __construct(ArrayAccess $config)
	{
        foreach ($config['types'] as $object) {
            $type = $object->getType();
            $tag  = $object->getTag();
            $this->rules[$type] = $object->convert()->getValue();
            $this->types[$tag]  = $object;
        }
	}

    /**
     * Add pipe
     * 
     * @param PipeInterface $pipe object
     */
    public function addPipe(PipeInterface $pipe)
    {
        $host = $this->formatPattern($pipe->getHost());
        $pipe->setHost($host);
        $this->pipes[] = $pipe;
    }

    /**
     * Add route
     * 
     * @param string         $name  route name
     * @param RouteInterface $route object
     */
    public function add(string $name, RouteInterface $route)
    {
		$unformatted = $route->getPattern();
        $this->validateUnformattedPattern($unformatted);
        $formatted = $this->formatPattern($unformatted);
        $host = $this->formatPattern($route->getHost());
        $route->setName($name);
        $route->setHost($host);
        $route->setPattern($formatted);
        $this->routes[$name] = $route;
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
     * Returns to all toutes
     * 
     * @return array
     */
    public function all() : array
    {
    	return $this->routes;
    }

    /**
     * Returns to all pipes
     * 
     * @return array
     */
    public function getPipes() : array
    {
        return $this->pipes;
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
     * Returns types
     * 
     * @return array
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * Returns to selected route
     * 
     * @param  string $name name
     * @return boolean
     */
    public function get(string $name)
    {
    	return isset($this->routes[$name]) ? $this->routes[$name] : false;
    }

    /**
     * Remove route
     * 
     * @param  string $name name
     * @return void
     */
    public function remove(string $name)
    {
    	unset($this->routes[$name]);
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
     * Validate route types
     * 
     * @param  string $pattern types
     * @return void
     */
    protected function validateUnformattedPattern(string $pattern)
    {
        foreach (explode('/', $pattern) as $value) {
            if ((substr($value, 0, 1) == '<' && substr($value, -1) == '>') && ! array_key_exists($value, $this->rules))  {
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