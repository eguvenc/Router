<?php

namespace Obullo\Router;

/**
 * UrlMapper Interface
 * 
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface UrlMapperInterface
{
    /**
     * Execute url mapper
     * 
     * @param  DispatcherInterface $dispatcher object
     * @return void
     */
    public function execute();

    /**
     * Set a bundle
     *
     * @param string $bundle name
     *
     * @return object FrontController
     */
    public function setBundle($bundle);

    /**
     * Set the class name
     *
     * @param string $class classname
     *
     * @return object FrontController
     */
    public function setClass($class);

    /**
     * Set current method
     *
     * @param string $method name
     *
     * @return object FrontController
     */
    public function setMethod($method);

    /**
     * Get bundle name
     *
     * @return string
     */
    public function getBundle();

    /**
     * Get class name
     *
     * @return string
     */
    public function getClass();

    /**
     * Get method name
     *
     * @return string
     */
    public function getMethod();

    /**
     * Get argument(s)
     * 
     * @param  string|null index $key string or number
     * @return mixed
     */
    public function getArgs($key = null);
}
