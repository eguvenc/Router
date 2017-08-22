<?php

namespace Obullo\Router;

use Obullo\Router\Filter\FilterTrait;
use Psr\Http\Message\RequestInterface as Request;

/**
 * Route group
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Group
{
    use AddTrait;
    use FilterTrait;

    protected $count  = 0;
    protected $request;
    protected $groups = array();

    /**
     * Constructor
     * 
     * @param object $request request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Queue group
     * 
     * @param string   $pattern  pattern
     * @param callable $callable callable
     * 
     * @return void
     */
    public function enqueue($pattern, $callable)
    {   
        ++$this->count;
        $this->groups[$this->count] = [
            'pattern' => $pattern,
            'callable' => $callable,
            'middlewares' => array()
        ];
    }

    /**
     * Dequeue the group array
     * 
     * @return array|null
     */
    public function dequeue()
    {
        return array_shift($this->groups);
    }

    /**
     * Returns to true if we have no group
     * 
     * @return boolean
     */
    public function isEmpty()
    {
        return empty($this->groups);
    }

    /**
     * Add middleware
     * 
     * @param string $name middleware name
     * @param array  $args arguments
     *
     * @return void
     */
    protected function middleware($name, array $args)
    {
        $this->groups[$this->count]['middlewares'][] = array('name' => $name, 'params' => $args);
    }

}
