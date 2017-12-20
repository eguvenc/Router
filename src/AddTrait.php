<?php

namespace Obullo\Router;

trait AddTrait
{
    /**
     * Add middleware
     *
     * @param string $name middleware
     * @param array  $args arguments
     * 
     * @return object router
     */
    public function add($name, $args = array())
    {
        if ($this->filter == null) {
            $this->middleware($name, $args);
            return $this;
        }
        if ($this->filter->hasMatch($this->getPath())) {
            $this->middleware($name, $args);
        }
        return $this;
    }

}