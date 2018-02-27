<?php

namespace Obullo\Router\Types;

use Obullo\Router\Type;

/**
 * Any <any:any>
 * 
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class AnyType extends Type
{
	/**
	 * Regex
	 *
	 * <any:any>  // before convertion
	 * %s = any   //  group name
	 * (?<any>.*) // after convertion
	 * 
	 * @var string
	 */
	protected $regex = '(?<%s>.*)';

	/**
	 * Php format
	 * 
	 * @param  number $value 
	 * @return int
	 */
	public function toPhp($value)
	{
		return (string)$value;
	}

	/**
	 * Url format
	 * 
	 * @param mixed $value
	 * @return string
	 */
	public function toUrl($value)
	{
		return sprintf('%s', $value);
	}
}
