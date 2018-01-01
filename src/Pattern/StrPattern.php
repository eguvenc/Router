<?php

namespace Obullo\Router\Pattern;

/**
 * Str pattern <group_name:str>
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class StrPattern extends AbstractPattern
{
	/**
	 * Regex
	 *
	 * <name:str>   // before convertion
	 * {GROUP_NAME} = name
	 * (?<name>\w+) // after convertion
	 * 
	 * @var string
	 */
	protected $regex = '(?<{GROUP_NAME}>\w+)';

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
