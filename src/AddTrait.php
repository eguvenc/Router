<?php

namespace Obullo\Router;

trait AddTrait
{
    /**
     * Add middleware
     *
     * @return object router
     */
    public function add()
    {
        $args = func_get_args();
        $name = $args[0];
        unset($args[0]);

        if ($this->condition == null) {
            $this->middleware($name, $args);
            return $this;
        }
        if ($this->condition->hasMatch()) {
            $this->middleware($name, $args);
        }
        return $this;
    }

}