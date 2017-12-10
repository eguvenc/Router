<?php

namespace Obullo\Router;

use Obullo\Middleware\Argument;
use Obullo\Router\Filter\FilterTrait;
use Obullo\Middleware\QueueInterface;

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

    protected $queue;
    protected $count = 0;
    protected $groups = array();

    /**
     * Constructor
     * 
     * @param QueueInterface|null $queue middleware
     */
    public function __construct(QueueInterface $queue = null)
    {
        $this->queue = $queue;
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
        $this->groups[$this->count] = ['pattern' => $pattern,'callable' => $callable];
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
        $this->queue->enqueue($name, new Argument($args));
    }
}
