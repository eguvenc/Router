<?php

class RegexTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->filter = new Obullo\Router\AddFilter\Regex("test/(\w+)/(\d+)");
    }

    public function testRegex()
    {
    	$this->assertTrue($this->filter->hasMatch("test/foo/1973"));
    }

    public function testOpposite()
    {
        $this->assertFalse($this->filter->hasMatch("test/foo/bar"));
    }

}