<?php

namespace Obullo\Router\Types;

use Obullo\Router\Type;

/**
 * Bool <bool:bool>
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class BoolType extends Type
{
	/**
	 * Regex
	 *
	 * <bool:true>   // before convertion
	 * %s = group name
	 * (?<bool>[0-1]) // after convertion
	 * 
	 * @var string
	 */
	protected $regex = '(?<%s>[0-1])';

	/**
	 * Php format
	 * 
	 * @param  number $value 
	 * @return int
	 */
	public function toPhp($value)
	{
		return (bool)$value;
	}

	/**
	 * Url format
	 * 
	 * @param mixed $value
	 * @return string
	 */
	public function toUrl($value)
	{
		return sprintf('%01d', $value);
	}
}
