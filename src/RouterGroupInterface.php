<?php

namespace Obullo\Router;

/**
 * RouteGroup
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface RouteGroupInterface
{
	/**
	 * Returns to route methods
	 * 
	 * @return array
	 */
	public function getMethods();

	/**
	 * Returns to handler
	 * 
	 * @return mixed
	 */
	public function getHandler();
}