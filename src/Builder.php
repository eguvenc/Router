<?php

namespace Obullo\Router;

use Obullo\Router\{
	Pipe,
    Route,
	RouteCollection
};
/**
 * Build / import route data
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Builder
{
	protected $collection;

	/**
	 * Constructor
	 * 
	 * @param LoaderInterface|null $loader loader
	 */
	public function __construct(RouteCollection $collection)
	{
		$this->collection = $collection;
	}

	public function build()
	{

	}

}