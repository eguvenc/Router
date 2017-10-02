<?php

namespace Obullo\Router;

/**
 * Dispatcher
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Dispatcher
{
    protected $path;
    protected $middleware;
    protected $args = array();

    public function __construct($path, $middleware = null)
    {
        $this->path = $path;
        $this->middleware = $middleware;
    }

    /**
     * Group process
     *
     * @return void
     */
    public function popGroup($request, $response, $group)
    {
        if ($group == null) {
            return;
        }
        $exp = explode("/", trim($this->path, "/"));
        $g   = $group->dequeue();

        if (in_array(trim($g['pattern'], "/"), $exp, true)) {
            $g['callable']($request, $response);
            if ($this->middleware != null) {
                $this->middleware->queue($g['middlewares']);
            }
        }
        if (! $group->isEmpty()) {
            $this->popGroup();
        }
    }

    public function dispatch($pattern)
    {
        $args = array();
        if (trim($pattern, "/") == trim($this->path, "/") ||
            preg_match('#^'.$pattern.'$#', $this->path, $args)
        ) {
            array_shift($args);
            $this->args = $args;
            return true;
        }
        return false;
    }

    public function getArgs()
    {
        return $this->args;
    }
}
