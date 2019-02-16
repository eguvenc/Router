<?php

namespace Obullo\Router\Matcher;

/**
 * Abstract matcher
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
abstract class Matcher
{
    protected $hostMatches = array();

    /**
     * Returns to true if route host matched
     * with current host otherwise false
     *
     * @param  string $host value
     * @return bool
     */
    public function matchHost($host = null) : bool
    {
        $routeHost = $this->route->getHost();
        if (empty($routeHost) || $routeHost == $host) {
            $this->hostMatches = (array)$host;
            return true;
        }
        $matches = array();
        $match = preg_match('#^'.$routeHost.'$#', $host, $matches);
        $this->hostMatches = $matches;
        return $match;
    }

    /**
     * Returns to true if route schemes contains
     * the current scheme otherwise false
     *
     * @param  string $scheme uri scheme
     * @return bool
     */
    public function matchScheme($scheme = null) : bool
    {
        $routeSchemes = $this->route->getSchemes();
        if (empty($routeSchemes)) {
            return true;
        }
        return in_array($scheme, $routeSchemes);
    }

    /**
     * Returns to host matches
     *
     * @return array
     */
    public function getHostMatches() : array
    {
        return $this->hostMatches;
    }
    
    /**
     * Match math
     *
     * @param  string $path path
     * @return bool
     */
    abstract public function matchPath(string $path) : bool;
}
