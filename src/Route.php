<?php

namespace Obullo\Router;

use Obullo\Router\{
	Stack\StackAwareTrait,
	Stack\StackAwareInterface
};
/**
 * Route
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Route implements StackAwareInterface, RouteInterface
{
    use StackAwareTrait;

	protected $name;
	protected $pattern;
	protected $args = array();
	protected $methods = array();
	protected $middlewares = array();
	protected $handler = null;

	 /**
     * Create a new route
     *
     * @param string|array $method  http method
     * @param string $pattern regex pattern
     * @param string|callable $handler handler
     * @param string|array $middlewares string|array
     *
     * @return object
     */
	public function __construct($method, string $pattern, $handler, $middlewares = null)
	{
        foreach ((array)$method as $name) {
        	$this->methods[] = strtoupper($name);
        }
        $this->handler = $handler;
        $this->pattern = '/'.ltrim($pattern, '/');
        $this->middlewares = (array)$middlewares;
	}

	/**
	 * Set pipe
	 * 
	 * @param string $pipe pipe
	 */
	public function setPipe(string $pipe)
	{
		$this->pattern = '/'.ltrim($pipe, '/').ltrim($this->pattern, '/');	
	}

	/**
	 * Set route name
	 * 
	 * @param string $name name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * Returns to path name
	 * 
	 * @return string
	 */
	public function getName() : string
	{
		return $this->name;
	}

	/**
	 * Returns to route methods
	 * 
	 * @return array
	 */
	public function getMethods() : array
	{
		return $this->methods;
	}

	/**
	 * Returns to handler
	 * 
	 * @return mixed
	 */
	public function getHandler()
	{
		return $this->handler;
	}

	/**
	 * Set arguments
	 * 
	 * @param array $args matched argumets
	 */
	public function setArgs(array $args)
	{
		$this->args = $args;
	}

    /**
     * Get argument(s)
     * 
     * @param  string|null index $key string or number
     * @return mixed
     */
    public function getArgs($key = null)
    {
        if ($key === null) {
            return $this->args;
        }
        return isset($this->args[$key]) ? $this->args[$key] : false;
    }

	/**
	 * Set pattern
	 * 
	 * @param string $pattern 
	 */
	public function setPattern(string $pattern)
	{
		$this->pattern = (string)$pattern;
	}

	/**
	 * Returns to pattern
	 * 
	 * @return string
	 */
	public function getPattern() : string
	{
		return $this->pattern;
	}
}