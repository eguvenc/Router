<?php

namespace Obullo\Router;

/**
 * Mapper
 *
 * Helps mapped route variables to set your application
 * 
 *     (B)undle / (C)lass / (M)ethod
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class UrlMapper implements UrlMapperInterface
{
    protected $class;
    protected $bundle;
    protected $method;
    protected $separator;
    protected $dispatcher;
    protected $args = array();
    protected $segments = array();

    /**
     * Constructor
     * 
     * @param DispatcherInterface $dispatcher object
     * @param array               $config     
     */
    public function __construct(DispatcherInterface $dispatcher, $config = array())
    {
        $this->path = $config['path'];
        $this->dispatcher = $dispatcher;
        $this->separator = isset($config['separator']) ? $config['separator'] : '->';
        $this->method = isset($config['default.method']) ? $config['default.method'] : 'index';
    }

    /**
     * Execute url mapper
     * 
     * @param  DispatcherInterface $dispatcher object
     * @return void
     */
    public function execute()
    {
        $this->args = $this->dispatcher->getArgs();
        $handler    = $this->dispatcher->getHandler();
        if (is_string($handler)){
            $this->segments = explode($this->separator, trim($handler, "/"));
        } else {
            $this->segments = explode("/", trim($this->path, "/"));
        }
        $this->mapHandler();
    }

    /**
     * Map handler
     * 
     * @return void
     */
    protected function mapHandler()
    {
        $this->setClass($this->segments[0]);
        if (! empty($this->segments[1])) {
            $this->setMethod($this->segments[1]);        
        }
    }

    /**
     * Set a bundle
     *
     * @param string $bundle name
     *
     * @return object FrontController
     */
    public function setBundle($bundle)
    {
        $this->bundle = $bundle;
        return $this;
    }

    /**
     * Set the class name
     *
     * @param string $class classname
     *
     * @return object FrontController
     */
    public function setClass($class)
    {
        $this->class = $class;
        return $this;
    }

    /**
     * Set current method
     *
     * @param string $method name
     *
     * @return object FrontController
     */
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    /**
     * Get bundle name
     *
     * @return string
     */
    public function getBundle()
    {
        return ucfirst($this->bundle); // bundle name first letter must be Uppercase
    }

    /**
     * Get class name
     *
     * @return string
     */
    public function getClass()
    {
        return ucfirst($this->class);  // class name first letter must be Uppercase
    }

    /**
     * Get method name
     *
     * @return string
     */
    public function getMethod()
    {
        return lcfirst($this->method); // method name first letter must be Lowercase
    }

    /**
     * Get argument(s)
     * 
     * @param  string|null index $key string or number
     * @return mixed
     */
    public function getArgs($key = null)
    {
        if ($key === null) {
            return $this->args;
        }
        return isset($this->args[$key]) ? $this->args[$key] : false;
    }

    /**
     * Set arguments
     * 
     * @param array $args mapper arguments
     * @return object
     */
    public function setArgs($args)
    {
        $this->args = $args;
        return $this;
    }

    /**
     * Unset bundle variable
     * 
     * @return object
     */
    public function unsetBundle()
    {
        $this->bundle = null;
        return $this;
    }

    /**
     * Unset class variable
     * 
     * @return object
     */
    public function unsetClass()
    {
        $this->class = null;
        return $this;
    }

    /**
     * Unset class variable
     * 
     * @return object
     */
    public function unsetMethod()
    {
        $this->method = null;
        return $this;
    }

   /**
     * Unset args
     * 
     * @return object
     */
    public function unsetArgs()
    {
        $this->args = array();
        return $this;
    }

}