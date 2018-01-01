<?php

namespace Obullo\Router\Pattern;

/**
 * Number pattern <group_name:int>
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class NumberPattern extends AbstractPattern
{
	/**
	 * Regex
	 *
	 * <number:str> 	  // before convertion
	 * {GROUP_NAME} = number
	 * (?<number>[0-9]+)  // after convertion
	 * 
	 * @var string
	 */
	protected $regex = '(?<{GROUP_NAME}>[0-9]+)';

	/**
	 * Set type
	 * 
	 * @return string
	 */
	public function getType()
	{
		return 'string';
	}
}
