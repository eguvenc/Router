<?php

namespace Obullo\Router;

/**
 * RouteRule
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface RouteRuleInterface
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