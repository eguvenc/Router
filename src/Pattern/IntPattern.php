<?php

namespace Obullo\Router\Pattern;

/**
 * Integer pattern <group_name:int>
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class IntPattern extends AbstractPattern
{
	/**
	 * Regex
	 *
	 * <id:int>   // before convertion
	 * {GROUP_NAME} = id
	 * (?<id>\d+) // after convertion
	 * 
	 * @var string
	 */
	protected $regex = '(?<{GROUP_NAME}>\d+)';

	/**
	 * Set type
	 * 
	 * @return string
	 */
	public function getType()
	{
		return 'integer';
	}
}
