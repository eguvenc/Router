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
	public function setPipe(string $pipe);

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
	 * Set host value
	 * 
	 * @param string $host host
	 */
	public function setHost($host);

	/**
	 * Returns to host
	 * 
	 * @return null|string
	 */
	public function getHost();

	/**
	 * Set schemes
	 * 
	 * @param array $schemes schemes
	 */
	public function setSchemes($schemes);

	/**
	 * Returns to schemes
	 * 
	 * @return array
	 */
	public function getSchemes() : array;

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
	public function setArguments(array $args);

    /**
     * Get one argument
     *
     * @return mixed
     */
    public function getArgument(string $key);

    /**
     * Get argument(s)
     *
     * @return mixed
     */
    public function getArguments() : array;

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