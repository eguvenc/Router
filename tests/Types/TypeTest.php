<?php

use Obullo\Router\Types\{
    AnyType,
    BoolType,
    FourDigitYearType,
    IntType,
    SlugType,
    StrType,
    TranslationType,
    TwoDigitDayType,
    TwoDigitMonthType
};

class TypeTest extends PHPUnit_Framework_TestCase
{
    public function testGetValue()
    {
        $this->assertEquals('(?<any>.*)', (new AnyType('<any:any>'))->convert()->getValue());
        $this->assertEquals('(?<status>[0-1])', (new BoolType('<bool:status>'))->convert()->getValue());
        $this->assertEquals('(?<year>[0-9]{4})', (new FourDigitYearType('<yyyy:year>'))->convert()->getValue());
        $this->assertEquals('(?<page>\d+)', (new IntType('<int:page>'))->convert()->getValue());
        $this->assertEquals('(?<slug>[\w-]+)', (new SlugType('<slug:slug>'))->convert()->getValue());
        $this->assertEquals('(?<slug_>[\w-_]+)', (new SlugType('<slug:slug_>', '(?<%s>[\w-_]+)'))->convert()->getValue());
        $this->assertEquals('(?<name>\w+)', (new StrType('<str:name>'))->convert()->getValue());
        $this->assertEquals('(?<locale>[a-z]{2})', (new TranslationType('<locale:locale>'))->convert()->getValue());
        $this->assertEquals('(?<day>[0-9]{2})', (new TwoDigitDayType('<dd:day>'))->convert()->getValue());
        $this->assertEquals('(?<month>[0-9]{2})', (new TwoDigitMonthType('<mm:month>'))->convert()->getValue());
    }

    public function testToPhp()
    {
        $this->assertInternalType('string', (new AnyType('<any:any>'))->toPhp('test'));
        $this->assertInternalType('boolean', (new BoolType('<bool:status>'))->toPhp('1'));
        $this->assertInternalType('boolean', (new BoolType('<bool:status>'))->toPhp('0'));
        $this->assertInternalType('integer', (new FourDigitYearType('<yyyy:year>'))->toPhp('1998'));
        $this->assertInternalType('integer', (new IntType('<int:page>'))->toPhp('12'));
        $this->assertInternalType('string', (new SlugType('<slug:slug>'))->toPhp('abcd-1923'));
        $this->assertInternalType('string', (new SlugType('<slug:slug_>', '(?<$name>[\w-_]+)$'))->toPhp('abcd-1923_'));
        $this->assertInternalType('string', (new StrType('<str:name>'))->toPhp('test'));
        $this->assertInternalType('string', (new TranslationType('<locale:locale>'))->toPhp('en'));
        $this->assertInternalType('integer', (new TwoDigitDayType('<dd:day>'))->toPhp('02'));
        $this->assertInternalType('integer', (new TwoDigitMonthType('<mm:month>'))->toPhp('11'));

        $this->assertEquals('test', (new AnyType('<any:any>'))->toPhp('test'));
        $this->assertEquals(true, (new BoolType('<bool:status>'))->toPhp('1'));
        $this->assertEquals(false, (new BoolType('<bool:status>'))->toPhp('0'));
        $this->assertEquals(1998, (new FourDigitYearType('<yyyy:year>'))->toPhp('1998'));
        $this->assertEquals(12, (new IntType('<int:page>'))->toPhp('12'));
        $this->assertEquals('abcd-1923', (new SlugType('<slug:slug>'))->toPhp('abcd-1923'));
        $this->assertEquals('abcd-1923_', (new SlugType('<slug:slug_>', '(?<$name>[\w-_]+)$'))->toPhp('abcd-1923_'));
        $this->assertEquals('test', (new StrType('<str:name>'))->toPhp('test'));
        $this->assertEquals('en', (new TranslationType('<locale:locale>'))->toPhp('en'));
        $this->assertEquals(02, (new TwoDigitDayType('<dd:day>'))->toPhp('02'));
        $this->assertEquals(11, (new TwoDigitMonthType('<mm:month>'))->toPhp('11'));
    }

    public function testToUrl()
    {
        $this->assertEquals('test', (new AnyType('<any:any>'))->toUrl('test'));
        $this->assertEquals('1', (new BoolType('<bool:status>'))->toUrl(1));
        $this->assertEquals('0', (new BoolType('<bool:status>'))->toUrl(0));
        $this->assertEquals('1998', (new FourDigitYearType('<yyyy:year>'))->toUrl('1998'));
        $this->assertEquals('12', (new IntType('<int:page>'))->toUrl('12'));
        $this->assertEquals('abcd-1923', (new SlugType('<slug:slug>'))->toUrl('abcd-1923'));
        $this->assertEquals('abcd-1923_', (new SlugType('<slug:slug_>', '(?<$name>[\w-_]+)$'))->toUrl('abcd-1923_'));
        $this->assertEquals('test', (new StrType('<str:name>'))->toUrl('test'));
        $this->assertEquals('en', (new TranslationType('<locale:locale>'))->toUrl('en'));
        $this->assertEquals('02', (new TwoDigitDayType('<dd:day>'))->toUrl('02'));
        $this->assertEquals('11', (new TwoDigitMonthType('<mm:month>'))->toUrl('11'));
    }

}