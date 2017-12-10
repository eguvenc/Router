<?php

use Obullo\Router\Group;
use Obullo\Middleware\Queue;

class GroupTest extends PHPUnit_Framework_TestCase
{
    protected $queue;
    protected $group;

    public function setUp()
    {
		$this->queue = new Queue;
		$this->queue->register('\App\Middleware\\');
        $this->group = new Group($this->queue);
    }

    public function testEnqueue()
    {
    	$this->group->enqueue("foo", function() {
    		return "bar";
    	});
    	$array = $this->group->dequeue();
    	$this->assertEquals($array['pattern'], "foo");
    	$this->assertEquals($array['callable'](), "bar");
    }

    public function testIsEmpty()
    {
    	$this->assertEquals(true, $this->group->isEmpty());
    	$this->group->enqueue("foo", function() {
    		return "bar";
    	});
    	$this->assertEquals(false, $this->group->isEmpty());
    	$this->group->dequeue();
    	$this->assertEquals(true, $this->group->isEmpty());
    }

    public function testAdd()
    {
    	$this->group->add("Dummy", array('foo', 'bar'));

    	$data 	= $this->queue->dequeue();
    	$params = $data['argument']->getParams();

    	$this->assertInstanceOf("App\Middleware\Dummy", $data['callable']);
    	$this->assertInstanceOf("Obullo\Middleware\Argument", $data['argument']);

    	$this->assertEquals($params[0], "foo");
    	$this->assertEquals($params[1], "bar");
    }

}