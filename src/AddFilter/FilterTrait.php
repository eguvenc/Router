<?php

namespace Obullo\Router\AddFilter;

trait FilterTrait
{
    protected $filter;

    /**
     * Create condition
     *
     * @param string $method method
     * @param mixed  $params parameters
     *
     * @return object
     */
    public function filter(FilterInterface $filter)
    {
        $this->filter = $filter;
        return $this;
    }
}
