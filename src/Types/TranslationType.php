<?php

namespace Obullo\Router\Types;

use Obullo\Router\Type;

/**
 * Translation <locale:locale>
 * 
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class TranslationType extends Type
{
	/**
	 * Regex
	 *
	 * <locale:locale>   // before convertion
	 * %s = locale //  group name
	 * (?<locale>[a-z]{2}) // after convertion
	 * 
	 * @var string
	 */
	protected $regex = '(?<%s>[a-z]{2})';

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
		return sprintf('%02s', $value);
	}
}
