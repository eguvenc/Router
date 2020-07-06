<?php

use PHPUnit\Framework\TestCase;
use Obullo\Router\Types\AnyType;
use Obullo\Router\Types\BoolType;
use Obullo\Router\Types\FourDigitYearType;
use Obullo\Router\Types\IntType;
use Obullo\Router\Types\SlugType;
use Obullo\Router\Types\StrType;
use Obullo\Router\Types\TranslationType;
use Obullo\Router\Types\TwoDigitDayType;
use Obullo\Router\Types\TwoDigitMonthType;

class TypeTest extends TestCase
{
    public function testGetValue()
    {
        $this->assertEquals('(?<any>.*)', (new AnyType('<any:any>'))->convert()->getValue());
        $this->assertEquals('(?<status>[0-1])', (new BoolType('<bool:status>'))->convert()->getValue());
        $this->assertEquals('(?<year>[0-9]{4})', (new FourDigitYearType('<yyyy:year>'))->convert()->getValue());
        $this->assertEquals('(?<page>\d+)', (new IntType('<int:page>'))->convert()->getValue());
        $this->assertEquals('(?<slug>[a-zA-Z0-9_-]+)', (new SlugType('<slug:slug>'))->convert()->getValue());
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
        $this->assertInternalType('string', (new SlugType('<slug:slug>'))->toPhp('abcd-1923_'));
        $this->assertInternalType('string', (new StrType('<str:name>'))->toPhp('test'));
        $this->assertInternalType('string', (new TranslationType('<locale:locale>'))->toPhp('en'));
        $this->assertInternalType('integer', (new TwoDigitDayType('<dd:day>'))->toPhp('02'));
        $this->assertInternalType('integer', (new TwoDigitMonthType('<mm:month>'))->toPhp('11'));

        $this->assertEquals('test', (new AnyType('<any:any>'))->toPhp('test'));
        $this->assertEquals(true, (new BoolType('<bool:status>'))->toPhp('1'));
        $this->assertEquals(false, (new BoolType('<bool:status>'))->toPhp('0'));
        $this->assertEquals(1998, (new FourDigitYearType('<yyyy:year>'))->toPhp('1998'));
        $this->assertEquals(12, (new IntType('<int:page>'))->toPhp('12'));
        $this->assertEquals('abcd-1923_', (new SlugType('<slug:slug>'))->toPhp('abcd-1923_'));
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
        $this->assertEquals('abcd-1923_', (new SlugType('<slug:slug>'))->toUrl('abcd-1923_'));
        $this->assertEquals('test', (new StrType('<str:name>'))->toUrl('test'));
        $this->assertEquals('en', (new TranslationType('<locale:locale>'))->toUrl('en'));
        $this->assertEquals('02', (new TwoDigitDayType('<dd:day>'))->toUrl('02'));
        $this->assertEquals('11', (new TwoDigitMonthType('<mm:month>'))->toUrl('11'));
    }

    public function testAnyTypeSetState()
    {
        $type = AnyType::__set_state(array(
             'regex' => '(?<%s>\\d+)',
             'tag' => 'id',
             'pattern' => '<int:id>',
             'value' => null,
             'tags' =>
            array(
              0 => 'int',
              1 => 'id',
            ),
        ));
        $this->assertEquals('(?<%s>\\d+)', $type->getRegex());
        $this->assertEquals('<int:id>', $type->getPattern());
        $this->assertEquals('id', $type->getTag());
        $this->assertEquals('int', $type->getTags()[0]);
        $this->assertEquals('id', $type->getTags()[1]);
    }

    public function testBoolTypeSetState()
    {
        $type = BoolType::__set_state(array(
             'regex' => '(?<%s>\\d+)',
             'tag' => 'id',
             'pattern' => '<int:id>',
             'value' => null,
             'tags' =>
            array(
              0 => 'int',
              1 => 'id',
            ),
        ));
        $this->assertEquals('(?<%s>\\d+)', $type->getRegex());
        $this->assertEquals('<int:id>', $type->getPattern());
        $this->assertEquals('id', $type->getTag());
        $this->assertEquals('int', $type->getTags()[0]);
        $this->assertEquals('id', $type->getTags()[1]);
    }

    public function testFourDigitYearTypeSetState()
    {
        $type = FourDigitYearType::__set_state(array(
             'regex' => '(?<%s>\\d+)',
             'tag' => 'id',
             'pattern' => '<int:id>',
             'value' => null,
             'tags' =>
            array(
              0 => 'int',
              1 => 'id',
            ),
        ));
        $this->assertEquals('(?<%s>\\d+)', $type->getRegex());
        $this->assertEquals('<int:id>', $type->getPattern());
        $this->assertEquals('id', $type->getTag());
        $this->assertEquals('int', $type->getTags()[0]);
        $this->assertEquals('id', $type->getTags()[1]);
    }

    public function testIntTypeSetState()
    {
        $type = IntType::__set_state(array(
             'regex' => '(?<%s>\\d+)',
             'tag' => 'id',
             'pattern' => '<int:id>',
             'value' => null,
             'tags' =>
            array(
              0 => 'int',
              1 => 'id',
            ),
        ));
        $this->assertEquals('(?<%s>\\d+)', $type->getRegex());
        $this->assertEquals('<int:id>', $type->getPattern());
        $this->assertEquals('id', $type->getTag());
        $this->assertEquals('int', $type->getTags()[0]);
        $this->assertEquals('id', $type->getTags()[1]);
    }

    public function testSlugTypeSetState()
    {
        $type = SlugType::__set_state(array(
             'regex' => '(?<%s>\\d+)',
             'tag' => 'id',
             'pattern' => '<int:id>',
             'value' => null,
             'tags' =>
            array(
              0 => 'int',
              1 => 'id',
            ),
        ));
        $this->assertEquals('(?<%s>\\d+)', $type->getRegex());
        $this->assertEquals('<int:id>', $type->getPattern());
        $this->assertEquals('id', $type->getTag());
        $this->assertEquals('int', $type->getTags()[0]);
        $this->assertEquals('id', $type->getTags()[1]);
    }

    public function testStrTypeSetState()
    {
        $type = StrType::__set_state(array(
             'regex' => '(?<%s>\\d+)',
             'tag' => 'id',
             'pattern' => '<int:id>',
             'value' => null,
             'tags' =>
            array(
              0 => 'int',
              1 => 'id',
            ),
        ));
        $this->assertEquals('(?<%s>\\d+)', $type->getRegex());
        $this->assertEquals('<int:id>', $type->getPattern());
        $this->assertEquals('id', $type->getTag());
        $this->assertEquals('int', $type->getTags()[0]);
        $this->assertEquals('id', $type->getTags()[1]);
    }

    public function testTranslationTypeSetState()
    {
        $type = TranslationType::__set_state(array(
             'regex' => '(?<%s>\\d+)',
             'tag' => 'id',
             'pattern' => '<int:id>',
             'value' => null,
             'tags' =>
            array(
              0 => 'int',
              1 => 'id',
            ),
        ));
        $this->assertEquals('(?<%s>\\d+)', $type->getRegex());
        $this->assertEquals('<int:id>', $type->getPattern());
        $this->assertEquals('id', $type->getTag());
        $this->assertEquals('int', $type->getTags()[0]);
        $this->assertEquals('id', $type->getTags()[1]);
    }

    public function testTwoDigitDayTypeSetState()
    {
        $type = TwoDigitDayType::__set_state(array(
             'regex' => '(?<%s>\\d+)',
             'tag' => 'id',
             'pattern' => '<int:id>',
             'value' => null,
             'tags' =>
            array(
              0 => 'int',
              1 => 'id',
            ),
        ));
        $this->assertEquals('(?<%s>\\d+)', $type->getRegex());
        $this->assertEquals('<int:id>', $type->getPattern());
        $this->assertEquals('id', $type->getTag());
        $this->assertEquals('int', $type->getTags()[0]);
        $this->assertEquals('id', $type->getTags()[1]);
    }

    public function testTwoDigitMonthTypeSetState()
    {
        $type = TwoDigitMonthType::__set_state(array(
             'regex' => '(?<%s>\\d+)',
             'tag' => 'id',
             'pattern' => '<int:id>',
             'value' => null,
             'tags' =>
            array(
              0 => 'int',
              1 => 'id',
            ),
        ));
        $this->assertEquals('(?<%s>\\d+)', $type->getRegex());
        $this->assertEquals('<int:id>', $type->getPattern());
        $this->assertEquals('id', $type->getTag());
        $this->assertEquals('int', $type->getTags()[0]);
        $this->assertEquals('id', $type->getTags()[1]);
    }
}
