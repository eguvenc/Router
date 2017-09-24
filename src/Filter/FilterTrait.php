<?php

namespace Obullo\Router\Filter;

use BadMethodCallException;

trait FilterTrait
{
    protected $condition;

    /**
     * Create condition
     *
     * @param string $method method
     * @param mixed  $params parameters
     *
     * @return object
     */
    public function filter($method, $params = null)
    {
        if ($this->condition == null) {
            $this->condition = new Condition($this->path);
        }
        if (! method_exists($this->condition, $method)) {
            throw new BadMethodCallException(
                sprintf(
                    "%s method is not defined in Condition class.",
                    $method
                )
            );
        }
        $this->condition->{$method}($params);
        return $this;
    }
}
