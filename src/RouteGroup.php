<?php

namespace Obullo\Router;

use Obullo\Router\{
	Stack\StackAwareTrait,
	Stack\StackAwareInterface,
	Exception\BadRouteGroupException
};
/**
 * Route group
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class RouteGroup implements StackAwareInterface, RouteGroupInterface
{
    use StackAwareTrait;

	protected $path;
	protected $callable;
	protected $middlewares = array();

	/**
	 * Constructor
	 * 
	 * @param string $path string group path
	 * @param object $callable class|function
	 */
	public function __construct($path, callable $callable)
	{
        if (! is_callable($callable)) {
        	throw new BadRouteGroupException('Group method second parameter must be callable.');
        }
        $this->path = $path;
        $this->callable = $callable;
	}

	/**
	 * Returns to group name with slash
	 * 
	 * @return string
	 */
	public function getPath() : string
	{
		return $this->path;
	}

	/**
	 * Returns to of the group
	 * 
	 * @return string
	 */
	public function getName() : string
	{
		return trim($this->path, "/");
	}

	/**
	 * Returns to callable
	 * 
	 * @return object
	 */
	public function getCallable() : callable
	{
		return $this->callable;
	}
}