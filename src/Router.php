<?php

namespace Obullo\Router;

use Obullo\Router\Matcher\RouteMatcher;
use Obullo\Router\Generator;
use Obullo\Router\Traits\MiddlewareAwareTrait;

/**
 * Router
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Router
{
    use MiddlewareAwareTrait;

    protected $path;
    protected $host;
    protected $route;
    protected $method;
    protected $scheme;
    protected $matcher;
    protected $handler;
    protected $collection;
    protected $match = false;
    protected $routes = array();

    /**
     * Constructor
     *
     * @param RouteCollection $collection collection
     */
    public function __construct(RouteCollection $collection)
    {
        $this->collection = $collection;
        $this->routes = $collection->all();

        $request = $collection->getContext();
        $this->path = $request->getPath();
        $this->method = $request->getMethod();
        $this->host = $request->getHost();
        $this->scheme = $request->getScheme();
    }

    /**
     * Route process
     *
     * @return false|RouteInterface
     */
    public function popRoute()
    {
        if (empty($this->routes)) {
            return false;
        }
        $route = array_shift($this->routes);
        $matcher = new RouteMatcher($route);

        if ($matcher->matchScheme($this->scheme) && $matcher->matchHost($this->host) && $matcher->matchPath($this->path)) {
            $this->match = true;
            $this->matcher = $matcher;
            $args = $matcher->getArguments();
            $newArgs = $this->formatArguments($args);
            $route->setArguments($newArgs);
            $this->route = $route;
            $this->middlewares = $route->getMiddlewares();
            return $route;
        }
        return $this->popRoute();
    }

    /**
     * Match
     *
     * @param  string $path   path
     * @param  string $host   host optional
     * @param  mixed  $scheme scheme optional
     * @return false|RouteInterface
     */
    public function match(string $path, $host = null, $scheme = null)
    {
        $this->path = $path;
        if ($host != '') {
            $this->host = $host;
        }
        if ($scheme != '') {
            $this->scheme = $scheme;
        }
        return $this->popRoute();
    }

    /**
     * Dispatch request
     *
     * @return false|RouteInterface
     */
    public function matchRequest()
    {
        return $this->popRoute();
    }

    /**
     * Returns to true if route match otherwise false
     *
     * @return boolean
     */
    public function hasMatch() : bool
    {
        return $this->match;
    }

    /**
     * Returns to matched route
     *
     * @return string
     */
    public function getMatchedRoute() : RouteInterface
    {
        return $this->route;
    }

    /**
     * Returns to matched host params
     *
     * @return array
     */
    public function getHostMatches() : array
    {
        return $this->matcher->getHostMatches();
    }

    /**
     * Returns to route collection
     *
     * @return object
     */
    public function getCollection() : RouteCollection
    {
        return $this->collection;
    }

    /**
     * Url generator helper
     *
     * @param  string $path   route path
     * @param  array  $params url parameters
     * @return string
     */
    public function url(string $path, $params = array())
    {
        $generator = new Generator($this->getCollection());
        return $generator->generate($path, $params);
    }

    /**
     * Format arguments
     *
     * @param $args matched arguments
     *
     * @return array arguments
     */
    protected function formatArguments(array $args) : array
    {
        $newArgs = array();
        $types = $this->collection->getPatterns();
        foreach ($args as $key => $value) {
            if (! is_numeric($key) && isset($types[$key])) {
                $newArgs[$key] = $types[$key]->toPhp($value);
            }
        }
        return $newArgs;
    }
}
