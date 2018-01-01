<?php

namespace Obullo\Router;

use SplQueue;
use Obullo\Router\RouteRuleInterface as RouteRule;

/**
 * RouteCollection
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class RouteCollection
{
	protected $group;
	protected $route;
	protected $args = array();
	protected $match = false;
	protected $groupPath = '';
	protected $regexPattern;
	protected $patterns = array();
	protected $nestedGroupCount = 0;
	// protected $tags = array();
	protected $types = array();
	protected $labels = array();

	public function __construct(array $patterns)
	{
		$this->group = new SplQueue;
		$this->route = new SplQueue;
		$this->patterns = $patterns;

		foreach ($patterns as $object) {
			$label 			= $object->getLabel();
			$firstTag 		= $object->getFirstTag();
			$convertedLabel = $object->convert();
			// $this->tags[$label]     = $firstTag;
			$this->labels[$label]   = $convertedLabel;
			$this->types[$firstTag] = $object->getType();
		}
	}

	public function setUriPath($path)
	{
		$this->path = $path;
		$this->resolvePath();
	}

	public function getUriPath()
	{
		return $this->path;
	}

	public function resolvePath()
	{
		$this->gPathArray = explode("/", trim($this->path, "/"));
	}

	public function attach($class)
	{
		$className = get_class($class);
		$exp = explode('\\', $className);
		$methodName = 'attach'.end($exp);
		$this->{$methodName}($class);
	}

	public function attachRewriteRule()
	{
        if (in_array($this->method, (array)$method)) {
            $pattern    = "/".ltrim($pattern, "/");
            $path       = preg_replace('#^'.$pattern.'$#', $rewrite, $this->path);
            $this->path = '/'.ltrim($path, '/');
        }

		// Rewrite kuralından sonra init edilmeli
		$this->resolvePath();
	}

	protected function attachRouteRule(RouteRule $routeRule)
	{
		$patternLabel = $routeRule->getPatternLabel();
		$pattern = str_replace(array_keys($this->labels), array_values($this->labels), $patternLabel);		
		// $index   = str_replace(array_keys($this->tags), array_values($this->tags), $patternLabel);

		$routeRule->setPattern($pattern);
		$this->route->enqueue($routeRule);

		// echo htmlspecialchars(print_r($this->tags, true));

		// echo "<pre>".htmlspecialchars($pattern)."</pre>";
	 	// echo "<pre>".htmlspecialchars($patternLabel)."</pre>";

		// $this->routes[$index] = $routeRule; // /welcome/index/id/name
		// echo $index."<br>";
		
	}

	protected function attachRouteGroup()
	{

	}

    /**
     * Add middleware
     *
     * @param string $name middleware name
     * @param array  $args arguments
     *
     * @return void
     */
    protected function middleware($name, array $args)
    {
        $this->queue->enqueue($name, new Argument($args));
    }

	/**
	 * Returns to all types
	 * 
	 * @return array
	 */
	public function getTypes()
	{
		return $this->types;
	}

	/**
	 * Returns defined patterns
	 * 
	 * @return array
	 */
	public function getPatterns()
	{
		return $this->patterns;
	}

	public function getRoutes()
	{
		return $this->routes;
	}

    /**
     * Route process
     * 
     * @return array|null
     */
    public function popRoute()
    {
        $routeRule = $this->route->dequeue();
        $path      = trim($this->path, "/");
        $pattern   = $routeRule->getPattern();
        $regexRule = '#^'.$pattern.'$#';
        $args = array();
        if ($path == $pattern OR preg_match($regexRule, $path, $args)) {
            array_shift($args);
            $this->args = $this->mapTypes($args);
            $this->match = true;
            $this->regexPattern = $regexRule;
            return $routeRule;
        }
        if (! $this->route->isEmpty()) {
            $routeRule = $this->popRoute();
            if (! empty($routeRule)) {
                return $routeRule;
            }
        }
        return null;
    }

    /**
     * Map arguments to type casting
     * 
     * @param $args arguments
     * 
     * @return array
     */
    protected function mapTypes($args)
    {
    	$newArgs = array();
    	foreach ($args as $key => $value) {
    		$newArgs[$key] = $value;
    		if (isset($this->types[$key])) {
    			switch ($this->types[$key]) {
    				case 'integer':
    					$newArgs[$key] = (int)$value;
    					break;
    				case 'string':
    					$newArgs[$key] = (string)$value;
    					break;
    				case 'boolean':
    					$newArgs[$key] = (boolean)$value;
    					break;
    				case 'float':
    					$newArgs[$key] = (float)$value;
    					break;	
    			}
    		}
    	}
    	return $newArgs;
    }

    /**
     * Returns to matched regex pattern
     * 
     * @return strign
     */
	public function getRegexPattern()
	{
		return $this->regexPattern;
	}

	/**
	 * Returns to matched arguments
	 * 
	 * @return array
	 */
    public function getArgs()
    {
    	return $this->args;
    }

    /**
     * Group process
     * 
     * @return mixed|null
     */
    public function popGroup()
    {
        $args = array();
        $handler = null;
        if ($this->group->isEmpty()) {
            return;
        }
        $g = $this->group->dequeue();
        $folder = trim($g['pattern'], "/");
        if (! empty($this->gPathArray[0]) && $this->gPathArray[0] == $folder) { // Execute the group if segment equal to group name.
            ++$this->nestedGroupCount;
            $this->groupPath.= $folder."/";
            $handler = $g['callable']($this->request, $this->response, $folder);
            array_shift($this->gPathArray); // Remove first segment from the group path array
        }
        if (! $this->group->isEmpty()) {
            $handler = $this->popGroup();
        }
        $this->nestedGroupCount = 0;
        return $handler;
    }

    /**
     * Returns to true if route match otherwise false
     * 
     * @return boolean
     */
    public function hasMatch()
    {
        return $this->match;
    }


}

