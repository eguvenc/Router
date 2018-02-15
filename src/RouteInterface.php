<?php

namespace Obullo\Router;

/**
 * Url path
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface RouteInterface
{
	/**
	 * Returns to path name
	 * 
	 * @return string
	 */
	public function getName() : string;

	/**
	 * Sets group based collection path
	 * 
	 * @param string $root root
	 */
	public function setRoot($root = null);

	/**
	 * Returns to root
	 * 
	 * @return string
	 */
	public function getRoot();
	
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
	 * Returns to pure route string
	 * 
	 * @return string
	 */
	public function getRule() : string;

	/**
	 * Set pattern
	 * 
	 * @param string $pattern pattern
	 */
	public function setPattern(string $pattern);

	/**
	 * Returns to pattern
	 * 
	 * @return string
	 */
	public function getPattern() : string;
}