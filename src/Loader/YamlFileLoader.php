<?php

namespace Obullo\Router\Loader;

use Obullo\Router\{
    RouteCollection,
    Builder
};
use Symfony\Component\Yaml\Yaml;

class YamlFileLoader implements LoaderInterface
{
    protected $routes = array();

    /**
     * Load file
     * 
     * @param string $file file
     */
    public function load(string $file)
    {
        $this->routes = Yaml::parseFile($file);
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