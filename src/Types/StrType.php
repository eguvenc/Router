<?php

namespace Obullo\Router\Types;

use Obullo\Router\Type;

/**
 * Str <str:name>
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class StrType extends Type
{
	/**
	 * Regex
	 *
	 * <name:str>   // before convertion
	 * $name 	 = group name
	 * (?<name>\w+) // after convertion
	 * 
	 * @var string
	 */
	protected $regex = '(?<$name>\w+)';

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
