<?php

namespace Obullo\Router;

/**
 * Route
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface RouteInterface
{
	/**
	 * Set pipe
	 * 
	 * @param string $pipe pipe
	 */
	public function setPipe(string $pipe) : RouteInterface;

	/**
	 * Set route name
	 * 
	 * @param string $name name
	 */
	public function setName($name);

	/**
	 * Returns to path name
	 * 
	 * @return string
	 */
	public function getName() : string;

	/**
	 * Returns to route methods
	 * 
	 * @return array
	 */
	public function getMethods() : array;

	/**
	 * Returns to handler
	 * 
	 * @return mixed
	 */
	public function getHandler();

	/**
	 * Set arguments
	 * 
	 * @param array $args matched argumets
	 */
	public function setArgs(array $args);

    /**
     * Get argument(s)
     * 
     * @param  string|null index $key string or number
     * @return mixed
     */
    public function getArgs($key = null);

	/**
	 * Set pattern
	 * 
	 * @param string $pattern 
	 */
	public function setPattern(string $pattern);

	/**
	 * Returns to pattern
	 * 
	 * @return string
	 */
	public function getPattern() : string;
}