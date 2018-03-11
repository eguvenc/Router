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
