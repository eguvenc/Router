<?php

namespace Obullo\Router;

/**
 * Path Controller
 *
 * Helps mapped route variables to set your application (folder/class/method)
 *
 * @copyright 2009-2017 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class PathController
{
    protected $class;
    protected $folder;
    protected $method;

    /**
     * Set the folder name
     *
     * @param string $folder folder
     *
     * @return object Router
     */
    public function setFolder($folder)
    {
        $this->folder = $folder;
        return $this;
    }

    /**
     * Set the class name
     *
     * @param string $class classname
     *
     * @return object Router
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
     * @return object Router
     */
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    /**
     * Get folder, folder name first letter must be uppercase
     *
     * @param string $separator get folder seperator
     *
     * @return string
     */
    public function getFolder($separator = '')
    {
        return (empty($this->folder)) ? '' : ucfirst($this->folder).$separator;
    }

    /**
     * Returns to current routed class name
     *
     * @return string
     */
    public function getClass()
    {
        return ucfirst($this->class);  // class name first letter must be uppercase
    }

    /**
     * Returns to current method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Returns php namespace of the current route
     *
     * @return string
     */
    public function getNamespace()
    {
        $folder = $this->getFolder();
        if (strpos($folder, "/") > 0) {  // Converts "Tests\Welcome/home" to Tests\Welcome\Home
            $exp = explode("/", $folder);
            $folder = trim(implode("\\", $exp), "\\");
        }
        $namespace = $folder;
        $namespace = trim($namespace, '\\');
        return (empty($namespace)) ? '' : $namespace.'\\';
    }

    /**
     * Clean dispatcher variables
     *
     * @return void
     */
    public function clear()
    {
        $this->class  = '';
        $this->folder = '';
    }
}
