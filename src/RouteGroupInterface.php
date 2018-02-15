<?php

namespace Obullo\Router;

/**
 * Route group
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface RouteGroupInterface
{
	/**
	 * Returns to group name with slash
	 * 
	 * @return string
	 */
	public function getPath() : string;

	/**
	 * Returns to of the group
	 * 
	 * @return string
	 */
	public function getName() : string;

	/**
	 * Returns to callable
	 * 
	 * @return object
	 */
	public function getCallable() : callable;
}