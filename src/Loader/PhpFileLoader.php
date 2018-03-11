<?php

namespace Obullo\Router\Loader;

use Obullo\Router\{
    RouteCollection,
    Builder,
    Exception\ParseException
};
class PhpFileLoader implements LoaderInterface
{
    protected $routes = array();

    /**
     * Load file
     * 
     * @param object
     */
    public function load(string $file) : LoaderInterface
    {
        if (! file_exists($file)) {
            throw new ParseException(
                sprintf('File "%s" does not exist.', $file)
            ); 
        }
        $this->routes = require $file;
        return $this;
    }

    /**
     * Returns to route data
     * 
     * @return array
     */
    public function all() : array
    {
        return $this->routes;
    }

    /**
     * Build collection
     * 
     * @param  RouteCollection $collection collection
     * @return RouteCollection object
     */
    public function build(RouteCollection $collection) : RouteCollection
    {
        $builder = new Builder($collection);
        return $builder->build($this->routes);
    }
}