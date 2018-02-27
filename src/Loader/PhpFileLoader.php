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
     * @param string $file file
     */
    public function load(string $file)
    {
        if (! file_exists($file)) {
            throw new ParseException(
                sprintf('File "%s" does not exist.', $file)
            ); 
        }
        $this->routes = require $file;
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