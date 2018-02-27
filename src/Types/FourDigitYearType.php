<?php

namespace Obullo\Router\Types;

use Obullo\Router\Type;

/**
 * Year <yyyy:year>
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class FourDigitYearType extends Type
{
	/**
	 * Regex
	 *
	 * <yyyy:year>   // before convertion
	 * %s = year //  group name
	 * (?<year>[0-9]{4}) // after convertion
	 * 
	 * @var string
	 */
	protected $regex = '(?<%s>[0-9]{4})';

	/**
	 * Php format
	 * 
	 * @param  number $value 
	 * @return int
	 */
	public function toPhp($value)
	{
		return (int)$value;
	}

	/**
	 * Url format
	 * 
	 * @param mixed $value
	 * @return string
	 */
	public function toUrl($value)
	{
		return sprintf('%04d', $value);
	}
}
