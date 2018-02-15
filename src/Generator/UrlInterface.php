<?php

namespace Obullo\Router;

/**
 * Url path
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface UrlInterface
{
	/**
	 * Returns to path
	 * 
	 * @return string
	 */
	public function make(string $url) : string;

}