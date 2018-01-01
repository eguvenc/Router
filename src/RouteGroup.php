<?php

namespace Obullo\Router;

/**
 * RouteGroup
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class RouteGroup implements RouteGroupInterface
{
	protected $pattern;
	protected $callable;

	public function __construct($pattern, $callable)
	{
        if (! is_callable($callable)) {
            throw new InvalidArgumentException("Group method second parameter must be callable.");
        }
        $this->pattern  = $pattern;
        $this->callable = $callable;
	}

	public function getPattern()
	{
		return $this->pattern;
	}

	public function getCallable()
	{
		return $this->callable;
	}

}