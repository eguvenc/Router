<?php

namespace Obullo\Router;

use SplQueue;
use Obullo\Middleware\Argument;
use Obullo\Middleware\QueueInterface;
use Obullo\Router\AddFilter\FilterTrait;

/**
 * Route group
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Group extends SplQueue
{
    use AddTrait;
    use FilterTrait;

    protected $queue;
    
    /**
     * Constructor
     * 
     * @param QueueInterface|null $queue middleware
     */
    public function __construct(RouterInterface $router, QueueInterface $queue = null)
    {
        $this->queue = $queue;
        $this->router = $router;
    }

    /**
     * Returns to path
     * 
     * @return string
     */
    public function getPath()
    {
        return $this->router->getPath();
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
