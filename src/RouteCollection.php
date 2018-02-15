<?php

namespace Obullo\Router;

use Obullo\Router\Exception\UndefinedTypeException;
use Obullo\Router\RequestContext;
use ArrayAccess;

/**
 * Collection
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class RouteCollection
{
    protected $root;
    protected $g = 0;
    protected $r = 0;
    protected $path;
    protected $handler;
    protected $methods;
    protected $request;
    protected $routeRule;
    protected $pathHandler;
    protected $requestContext;
    protected $route = array();
    protected $group = array();
    protected $match = false;
    protected $types = array();
    protected $rules = array();
    protected $groupCount = 0;
    protected $middlewares = array();
    protected $groupMatches  = array();
    protected $groupSegments = array();
	protected $routeSegments = array();

    /**
     * Constructor
     * 
     * @param RequestContext $context context
     * @param ArrayAccess    $config  config
     */
    public function __construct(RequestContext $context, ArrayAccess $config)
    {
        $this->requestContext = $context;
        $this->path = trim($context->getPath(), '/');
        foreach ($config['types'] as $object) {
            $type = $object->getType();
            $tag  = $object->getTag();
            $this->rules[$type] = $object->convert()->getValue();
            $this->types[$tag]  = $object;
        }
        $this->routeSegments = $this->groupSegments = explode('/', $this->getPath());
    }

    /**
     * Build route variables
     * 
     * @return object
     */
    public function build()
    {
        return $this;
    }

    /**
     * Add route
     * 
     * @param mixed  $name name of the route
     * @param mixed  $method  method name it can be array
     * @param string $pattern regex pattern
     * @param mixed  $handler string or callable
     * 
     * @return void
     */
    public function route($name, $method, $pattern, $handler = null)
    {
        $this->validatePattern($pattern);
        $this->route[$name] = new Route($name, $method, $pattern, $handler);
        $this->route[$name]->setRoot($this->root);
        $pattern = str_replace(
            array_keys($this->rules),
            array_values($this->rules),
            $this->route[$name]->getRule()
        );
        $this->route[$name]->setPattern($pattern);
        return $this->route[$name];
    }

    /**
     * Create group
     * 
     * @param  string   $pattern  pattern
     * @param  callable $callable callable
     * @return void
     */
    public function group($pattern, callable $callable)
    {
        ++$this->g;
        $this->group[$this->g] = new RouteGroup($pattern, $callable);
        return $this->group[$this->g];
    }

    /**
     * Route process
     * 
     * @return array|null
     */
    public function popRoute()
    {
        if (0 === count($this->route)) {
            return;
        }
        $routeRule = array_shift($this->route);
        $pattern   = $routeRule->getPattern();
        $regexRule = '#^'.$pattern.'$#';
        $args = array();
        if ($this->getPath() == $pattern OR preg_match($regexRule, $this->getPath(), $args)) {
            array_shift($args);
            $this->match = true;
            $newArgs = $this->formatArgs($args);
            $routeRule->setArgs($newArgs);
            $this->routeRule = $routeRule;
            $this->buildStack($routeRule);
            return $routeRule;
        }
        if (0 !== count($this->route)) {
            $routeRule = $this->popRoute();
            if (null != $routeRule) {
                return $routeRule;
            }
        }
        return null;
    }

    /**
     * Group process
     * 
     * @return mixed|null
     */
    public function popGroup()
    {
        if (0 === count($this->group)) { // If group empty
            return;
        }
        $args = array();
        $handler = null;
        $group = array_shift($this->group);
        $name     = $group->getName();
        $callable = $group->getCallable();
        if (! empty($this->groupSegments[0]) && $this->groupSegments[0] == $name) { // Run group if segment equal to group name.
            ++$this->groupCount;
            $this->groupMatches[] = $group;
            $this->root.= $name.'/';
            $handler = $callable($group);
            array_shift($this->groupSegments); // Remove first segment from the group path array
            $this->buildStack($group);
        }
        if (0 !== count($this->group)) {
            $handler = $this->popGroup();
        }
        $this->groupCount = 0;
        return $handler;
    }

    /**
     * Dispatch request
     * 
     * @return object
     */
    public function matchRequest() : self
    {
        $g = $this->popGroup();
        $r = $this->popRoute();

        if ($this->hasRouteMatch()) {
            $this->handler = $r->getHandler();
            $this->methods = $r->getMethods();
        }
        return $this;
    }

    /**
     * Returns to path
     * 
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Returns to stack handler object
     * 
     * @return object
     */
    public function getStack() : array
    {
        return $this->middlewares;
    }

    /**
     * Returns to true if route match otherwise false
     * 
     * @return boolean
     */
    public function hasRouteMatch() : bool
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
        return $this->routeRule;
    }

    /**
     * Returns to true if group match otherwise false
     * 
     * @return boolean
     */
    public function hasGroupMatch() : bool
    {
        return empty($this->groupMatches) ? false : true;
    }

    /**
     * Returns to matched group
     * 
     * @return object|boolean
     */
    public function getMatchedGroup(int $key = 0)
    {
        return isset($this->groupMatches[$key]) ? $this->groupMatches[$key] : false;
    }

    /**
     * Returns to all matched groups
     * 
     * @return array
     */
    public function getMatchedGroups() : array
    {
        return $this->groupMatches;
    }

    /**
     * Returns to handler
     * 
     * @return mixed
     */
    public function getMatchedHandler()
    {
        return $this->handler;
    }

    /**
     * Validate route types
     * 
     * @param  string $pattern types
     * @return void
     */
    protected function validatePattern(string $pattern)
    {
        foreach (explode('/', $pattern) as $value) {
            if ((substr($value, 0, 1) == '<' && substr($value, -1) == '>') && ! array_key_exists($value, $this->rules))  {
                throw new UndefinedTypeException(
                    sprintf(
                        'The route type %s you used is undefined.',
                        $value
                    )
                );
            }
        }
    }

    /**
     * Format arguments
     * 
     * @param $args matched arguments
     * 
     * @return array arguments
     */
    protected function formatArgs(array $args) : array
    {
        $newArgs  = array();
        foreach ($args as $key => $value) {
            if (! is_numeric($key) && isset($this->types[$key])) {
                $newArgs[$key] = $this->types[$key]->toPhp($value);
            }
        }
        return $newArgs;
    }
            
    /**
     * Attach middlewares to stack object
     * 
     * @param RouteRule|RouteGroup $object route objects
     */
    protected function buildStack($object)
    {
        foreach ($object->getStack() as $value) {
            $this->middlewares[] = $value;
        }
    }
}