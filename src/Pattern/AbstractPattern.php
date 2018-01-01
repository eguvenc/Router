<?php

namespace Obullo\Router\Pattern;

/**
 * Abstract pattern <group_name:int>
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
abstract class AbstractPattern
{
	protected $label;
	protected $firstTag;

	/**
	 * Contructor
	 * 
	 * @param string $label regex label
	 */
	public function __construct($label)
	{
		$this->label = $label;
		$label = rtrim(ltrim($label, '<'),'>');
		$exp   = explode(':', $label);
		$this->firstTag = $exp[0];
	}

	/**
	 * Returns to regex label
	 * 
	 * @return boolean
	 */
	public function getLabel()
	{
		return $this->label;
	}

	/**
	 * Returns to regex equivalent value of current pattern
	 * 
	 * @return boolean
	 */
	public function getRegex()
	{
		return $this->regex;
	}

	/**
	 * Returns to short name of class (e.q. int,str,year)
	 * 
	 * @return string
	 */
	public function getShortName()
	{
		$class = new \ReflectionClass($this);
		$name  = substr($class->getShortName(), 0, -7);
		return strtolower($name);
	}

	/**
	 * Convert label to fast route format
	 * 
	 * @return boolean
	 */
	public function convert()
	{
		return str_replace('{GROUP_NAME}', $this->firstTag, $this->regex);
	}

	/**
	 * Returns to first tag of label
	 * 
	 * @return string
	 */
	public function getFirstTag()
	{
		return $this->firstTag;
	}
}
