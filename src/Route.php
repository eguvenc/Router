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
	protected $host;
	protected $pattern;
	protected $args = array();
	protected $methods = array();
	protected $middlewares = array();
	protected $handler = null;
	protected $schemes = array();

	 /**
     * Create a new route
     *
     * @param string|array $method http method
     * @param string $pattern regex pattern
     * @param string|callable $handler handler
     * @param string|array $middlewares string|array
     * @param string $host the host pattern to match
     * @param string|array $schemes A required URI scheme or an array of restricted schemes
     *
     * @return object
     */
	public function __construct($method, string $pattern, $handler, $middlewares = array(), $host = null, $schemes = array())
	{
        foreach ((array)$method as $name) {
        	$this->methods[] = strtoupper($name);
        }
        $this->handler = $handler;
        $this->pattern = '/'.ltrim($pattern, '/');
        $this->middlewares = (array)$middlewares;
        $this->host = $host;
        $this->schemes = (array)$schemes;
	}

	/**
	 * Set pipe
	 * 
	 * @param string $pipe pipe
	 * @return object
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
	 * Set host value
	 * 
	 * @param string $host host
	 */
	public function setHost($host)
	{
		$this->host = $host;
	}

	/**
	 * Returns to host
	 * 
	 * @return null|string
	 */
	public function getHost()
	{
		return $this->host;
	}

	/**
	 * Set schemes
	 * 
	 * @param array $schemes schemes
	 */
	public function setSchemes($schemes)
	{
		$this->schemes = (array)$schemes;
	}

	/**
	 * Returns to schemes
	 * 
	 * @return array
	 */
	public function getSchemes() : array
	{
		return $this->schemes;
	}

	/**
	 * Returns to middleware class names
	 * 
	 * @return array
	 */
	public function getMiddlewares() : array
	{
		return $this->middlewares;
	}

	/**
	 * Set arguments
	 * 
	 * @param array $args matched argumets
	 */
	public function setArguments(array $args)
	{
		$this->args = $args;
	}

	/**
	 * Remove argument
	 * 
	 * @param  string $key name
	 * @return void
	 */
	public function removeArgument(string $key)
	{
		unset($this->args[$key]);
	}

    /**
     * Get argument
     * 
     * @param  string index $key string
     * @return mixed
     */
    public function getArgument(string $key)
    {
        return isset($this->args[$key]) ? $this->args[$key] : false;
    }

    /**
     * Get argument(s)
     * 
     * @param  string|null index $key string or number
     * @return mixed
     */
    public function getArguments() : array
    {
        return $this->args;
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