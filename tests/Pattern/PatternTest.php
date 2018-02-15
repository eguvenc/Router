<?php

class PatternTest extends PHPUnit_Framework_TestCase
{
    public function testGetValue()
    {
        $this->assertEquals('(?<any>.*)', (new Obullo\Router\Pattern\AnyPattern('<any:str>'))->convert()->getValue());
        $this->assertEquals('(?<true>[0-1])', (new Obullo\Router\Pattern\BoolPattern('<true:bool>'))->convert()->getValue());
        $this->assertEquals('(?<false>[0-1])', (new Obullo\Router\Pattern\BoolPattern('<false:bool>'))->convert()->getValue());
        $this->assertEquals('(?<page>\d+)', (new Obullo\Router\Pattern\IntPattern('<page:int>'))->convert()->getValue());
        $this->assertEquals('(?<number>[0-9]+)', (new Obullo\Router\Pattern\NumberPattern('<number:int>'))->convert()->getValue());
        $this->assertEquals('(?<number2>[0-9]+)', (new Obullo\Router\Pattern\NumberPattern('<number2:int>'))->convert()->getValue());
        $this->assertEquals('(?<slug>[\w-]+)$', (new Obullo\Router\Pattern\SlugPattern('<slug:str>'))->convert()->getValue());
        $this->assertEquals('(?<slug_>[\w-_]+)$', (new Obullo\Router\Pattern\SlugPattern('<slug_:str>', '(?<$name>[\w-_]+)$'))->convert()->getValue());
        $this->assertEquals('(?<word>\w+)', (new Obullo\Router\Pattern\StrPattern('<word:str>'))->convert()->getValue());
    }

    public function testGetType()
    {
        $this->assertEquals('int', (new Obullo\Router\Pattern\AnyPattern('<any:int>'))->convert()->getType());
        $this->assertEquals('bool', (new Obullo\Router\Pattern\AnyPattern('<any:bool>'))->convert()->getType());
        $this->assertEquals('float', (new Obullo\Router\Pattern\AnyPattern('<any:float>'))->convert()->getType());
        $this->assertEquals('str', (new Obullo\Router\Pattern\AnyPattern('<any:str>'))->convert()->getType());

        $this->assertEquals('bool', (new Obullo\Router\Pattern\BoolPattern('<true:bool>'))->convert()->getType());
        $this->assertEquals('bool', (new Obullo\Router\Pattern\BoolPattern('<false:bool>'))->convert()->getType());
        $this->assertEquals('int', (new Obullo\Router\Pattern\IntPattern('<page:int>'))->convert()->getType());
        $this->assertEquals('int', (new Obullo\Router\Pattern\NumberPattern('<number:int>'))->convert()->getType());
        $this->assertEquals('str', (new Obullo\Router\Pattern\SlugPattern('<slug:str>'))->convert()->getType());
        $this->assertEquals('str', (new Obullo\Router\Pattern\SlugPattern('<slug_:str>', '(?<$name>[\w-_]+)$'))->convert()->getType());
        $this->assertEquals('str', (new Obullo\Router\Pattern\StrPattern('<word:str>'))->convert()->getType());
    }
}