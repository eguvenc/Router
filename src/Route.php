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

	protected $root;
	protected $name;
	protected $rule;
	protected $pattern;
	protected $args = array();
	protected $methods = array();
	protected $middlewares = array();
	protected $handler = null;

	 /**
     * Create a new path
     *
     * @param string $name route name
     * @param string|array $method  http method
     * @param string $pattern regex pattern
     * @param mixed  $handler null
     *
     * @return object
     */
	public function __construct(string $name, $method, string $pattern, $handler = null)
	{
		$this->name = $name;
        $rule = trim($pattern, "/");
        $this->methods = (array)$method;
        $this->handler = $handler;
        $this->rule = $rule;
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
	 * Sets group based collection path
	 * 
	 * @param string $root root
	 */
	public function setRoot($root = null)
	{
		$this->root = $root;
	}

	/**
	 * Returns to root
	 * 
	 * @return string
	 */
	public function getRoot()
	{
		return $this->root;
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
	 * Returns to pure route pattern (static)
	 * 
	 * @return string
	 */
	public function getRule() : string
	{
		return $this->rule;
	}

	/**
	 * Set pattern
	 * 
	 * @param string $pattern pattern
	 */
	public function setPattern(string $pattern)
	{
		$this->pattern = $pattern;
	}

	/**
	 * Returns to pattern (dynamic)
	 * 
	 * @return string
	 */
	public function getPattern() : string
	{
		return $this->getRoot().$this->pattern;
	}
}