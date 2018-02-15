<?php

namespace Obullo\Router\Loader;

use Obullo\Router\{
    Exception\BadRouteException,
    Exception\RouteLengthException
};
use Symfony\Component\Yaml\Yaml;

class YamlFileLoader
{
    protected $file;

    /**
     * Load file
     * 
     * @param string $file file
     * @return object
     */
	public function load($file) : self
	{
        $this->file = $file;
        return $this;
	}

    /**
     * Parse file
     * 
     * @return void
     */
    public function parse()
    {
        $data = Yaml::parseFile($this->file);

        // var_dump($data);

        foreach ($data as $name => $route) {

            if (strpos($name, '/') === false) { // routes
                Self::ValidateRoute($name, $route);
                $method = empty($route['method']) ? 'GET' : $route['method'];
                $path   = $route['path'] == '/' ? $route['path'] : trim($route['path'], '/');
                $this->collection->add(
                    $name,
                    new Route(explode(' ',$method), $path, $route['handler'])
                );
            } else {  // route groups



            }
        }

    /*
  ["route_home"]=&gt;
  array(2) {
    ["welcome"]=&gt;
    array(4) {
      ["method"]=&gt;
      string(8) "GET POST"
      ["path"]=&gt;
      string(1) "/"
      ["handler"]=&gt;
      string(39) "App\Controller\WelcomeController::index"
      ["middleware"]=&gt;
      string(19) "App\Middleware\Auth"
    }
    ["dummy"]=&gt;
    array(3) {
      ["method"]=&gt;
      string(3) "GET"
      ["path"]=&gt;
      string(14) "/welcome/dummy"
      ["handler"]=&gt;
      string(37) "App\Controller\DummyController::index"
    }
  }
}
*/
    }

    protected function parseGroup(array $routes)
    {
        foreach ($routes as $route) {
            
        }
    }

    /**
     * Validate route

     * @param  string $name  name
     * @param  array  $route route
     * 
     * @return void
     */
    protected static function validateRoute(string $name, array $route)
    {
        if (empty($name)) {
            throw new BadRouteException('Route name is undefined.');
        }
        if (empty($route['path'])) {
            throw new BadRouteException('Route path is undefined.');
        }
        if (empty($route['handler'])) {
            throw new BadRouteException('Route handler is undefined.');
        }
    }
}