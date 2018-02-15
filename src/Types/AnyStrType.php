<?php

namespace Obullo\Router\Types;

use Obullo\Router\Type;

/**
 * Any <str:any>
 * 
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class AnyStrType extends Type
{
	/**
	 * Regex
	 *
	 * <any:str>   // before convertion
	 * $name = any //  group name
	 * (?<any>.*) // after convertion
	 * 
	 * @var string
	 */
	protected $regex = '(?<$name>.*)';

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
