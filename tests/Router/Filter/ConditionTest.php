<?php

class ConditionTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->condition = new Obullo\Router\Filter\Condition("test/foo/123");
    }

    public function testContains()
    {
    	$this->condition->contains(['test/foo/123', 'test/foo/1234', 'test/foo']);
    	$this->assertTrue($this->condition->hasMatch());
    	$this->condition->contains(['tes']);
    	$this->assertTrue($this->condition->hasMatch());
    	$this->condition->contains(['test/foo']);
    	$this->assertTrue($this->condition->hasMatch());
    	$this->condition->contains(['foo']);
    	$this->assertTrue($this->condition->hasMatch());
    }

    public function testNotContains()
    {
    	$this->condition->notContains(['test/foo/125', 'test/foo/1234']);
    	$this->assertTrue($this->condition->hasMatch());
    }

    public function testRegex()
    {
    	$this->condition->regex('test/(\w+)/(\d+)');
    	$this->assertTrue($this->condition->hasMatch());
    }

    public function testNotRegex()
    {
    	$this->condition->notRegex('test/(\d+)/(\w+)');
    	$this->assertTrue($this->condition->hasMatch());
    }

}