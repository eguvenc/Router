<?php

namespace Obullo\Router;

/**
 * RouteCollection Interface
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface RouteCollectionInterface
{
    /**
     * Add route
     *
     * @param RouteInterface $route object
     */
    public function add(RouteInterface $route);

    /**
     * Add variable
     *
     * @param string $name var name
     * @param array $data array data
     */
    public function addVariable(string $name, array $data);

    /**
     * Returns to variable data
     *
     * @param  string $name var name
     * @return @UndefinedVariableException|array var data
     */
    public function getVariable(string $name);

    /**
     * Add host to current route
     *
     * @param string $host name
     */
    public function host($host) : Self;

    /**
     * Add scheme to current route
     *
     * @param string|array scheme name
     */
    public function scheme($scheme) : Self;

    /**
     * Add middleware to current route
     *
     * @param string|array middleware class name
     */
    public function middleware($middleware) : Self;

    /**
     * Returns to number of routes
     *
     * @return int
     */
    public function count() : int;

    /**
     * Returns to all routes
     *
     * @return array
     */
    public function all() : array;

    /**
     * Gets the current RouteCollection as an Iterator that includes all routes.
     *
     * It implements IteratorAggregate.
     *
     * @return ArrayIterator object
     */
    public function getIterator();

    /**
     * Returns to pattern object
     *
     * @return array
     */
    public function getPattern() : Pattern;

    /**
     * Returns to selected route
     *
     * @param  string $name name
     * @return boolean
     */
    public function get(string $name);

    /**
     * Remove route
     *
     * @param  string $name name
     * @return void
     */
    public function remove(string $name);
}