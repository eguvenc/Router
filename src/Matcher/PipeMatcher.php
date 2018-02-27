<?php

namespace Obullo\Router\Matcher;

use Obullo\Router\PipeInterface;

/**
 * Pipe matcher
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class PipeMatcher extends Matcher
{
	protected $route;

	/**
	 * Constructor
	 * 
	 * @param PipeInterface $pipe pipe
	 */
	public function __construct(PipeInterface $pipe)
	{
		$this->route = $pipe;
	}

	/**
     * Returns to true if route matched with path otherwise false
     * 
     * @param  string $path path
     * @return boolean
     */
    public function matchPath(string $path) : bool
    {
        $matchedUri = substr(ltrim($path, '/'), 0, strlen($this->route->getPipe()));
    	if ($this->route->getPipe() == $matchedUri) {
    		return true;
    	}
    	return false;
    }
}