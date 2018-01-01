<?php

class GroupTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
		$this->queue = new Obullo\Middleware\Queue;
		$this->queue->register('\App\Middleware\\');

        $this->createRequest("http://example.com/welcome/index");
        $this->group = new Obullo\Router\Group($this->router, $this->queue);
    }

    public function testEnqueue()
    {
    	$this->group->enqueue([
            'pattern' => 'foo',
            'callable' => function() {
                return "bar";
            }
        ]);
    	$array = $this->group->dequeue();
    	$this->assertEquals($array['pattern'], "foo");
    	$this->assertEquals($array['callable'](), "bar");
    }

    public function testIsEmpty()
    {
    	$this->assertEquals(true, $this->group->isEmpty());
        $this->group->enqueue([
            'pattern' => 'foo',
            'callable' => function() {
                return "bar";
            }
        ]);
    	$this->assertEquals(false, $this->group->isEmpty());
    	$this->group->dequeue();
    	$this->assertEquals(true, $this->group->isEmpty());
    }

    public function testAdd()
    {
    	$this->group->add("Dummy", array('foo', 'bar'));

    	$data 	= $this->queue[0];
    	$params = $data['argument']->getParams();

    	$this->assertInstanceOf("App\Middleware\Dummy", $data['callable']);
    	$this->assertInstanceOf("Obullo\Middleware\Argument", $data['argument']);

    	$this->assertEquals($params[0], "foo");
    	$this->assertEquals($params[1], "bar");
    }

    public function testGetPath()
    {
        $this->assertEquals("/welcome/index", $this->group->getPath());
    }

    protected function createRequest($uri)
    {
        // Create a request
        $request  = Zend\Diactoros\ServerRequestFactory::fromGlobals();
        $request  = $request->withUri(new Zend\Diactoros\Uri($uri));

        $response = new Zend\Diactoros\Response;
        $this->router = new Obullo\Router\Router($request, $response, $this->queue);
        $this->mapper = new Obullo\Router\UrlMapper($this->router);
    }

}