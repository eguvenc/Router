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
	public function __construct($method, string $pattern, $handler, $middlewares = array(), ?string $host = '', $schemes = array())
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
	public function setPipe(string $pipe) : RouteInterface
	{
		$this->pattern = '/'.ltrim($pipe, '/').ltrim($this->pattern, '/');
		return $this;	
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
	public function setHost(string $host)
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
	 * Returns to schemes
	 * 
	 * @return array
	 */
	public function getSchemes() : array
	{
		return $this->schemes;
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