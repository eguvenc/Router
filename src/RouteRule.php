<?php

namespace Obullo\Router;

/**
 * RouteRule
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class RouteRule implements RouteRuleInterface
{
	protected $pattern;
	protected $patternLabel;
	protected $args = array();
	protected $methods = array();
	protected $handler = null;

	 /**
     * Create a new route
     *
     * @param string $method  method
     * @param string $pattern regex pattern
     * @param mixed  $handler null
     *
     * @return object
     */
	public function __construct($method, $pattern, $handler = null)
	{
        $rule = trim($pattern, "/");
        $this->methods = (array)$method;
        $this->handler = $handler;
        $this->patternLabel = $rule;
	}

	/**
	 * Returns to route methods
	 * 
	 * @return array
	 */
	public function getMethods()
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
	 * Returns to pure route string
	 * 
	 * @return string
	 */
	public function getPatternLabel()
	{
		return $this->patternLabel;
	}

	/**
	 * Set pattern
	 * 
	 * @param string $pattern pattern
	 */
	public function setPattern($pattern)
	{
		$this->pattern = $pattern;
	}

	/**
	 * Returns to pattern
	 * 
	 * @return string
	 */
	public function getPattern()
	{
		return $this->pattern;
	}
}