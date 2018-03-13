<?php

namespace Obullo\Router;

/**
 * Generator interface
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface GeneratorInterface
{
	/**
	 * Generate url
	 * 
	 * @param  string $name       route name
	 * @param  array  $parameters url parameters
	 * @return string
	 */
	public function generate(string $name, $parameters = array());
}