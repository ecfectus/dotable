<?php

namespace Ecfectus\Dotable\Test;

use Ecfectus\Dotable\Dotable;
use PHPUnit\Framework\TestCase;


class DotableTest extends TestCase
{
    public function testSet()
    {
        $d = new Dotable([]);
        $d->set('one', 1);
        $this->assertEquals(['one' => 1], $d->getValues());
    }
    public function testSetOverride()
    {
        $d = new Dotable(['one' => 1]);
        $d->set('one', 2);
        $this->assertEquals(['one' => 2], $d->getValues());
    }
    public function testSetPath()
    {
        $d = new Dotable(['one' => ['two' => 1]]);
        $d->set('one.two', 2);
        $this->assertEquals(['one' => ['two' => 2]], $d->getValues());
    }
    public function testPathAppend()
    {
        $d = new Dotable(['one' => ['two' => 1]]);
        $d->set('one.other', 1);
        $this->assertEquals(['one' => ['two' => 1, 'other' => 1]], $d->getValues());
    }
    public function testSetAppend()
    {
        $d = new Dotable(['one' => ['two' => 1]]);
        $d->set('two', 2);
        $this->assertEquals(['one' => ['two' => 1], 'two' => 2], $d->getValues());
    }
    public function testSetAppendArray()
    {
        $d = new Dotable(['one' => ['two' => 1]]);
        $d->set('one', ['two' => 2]);
        $this->assertEquals(['one' => ['two' => 2]], $d->getValues());
    }
    public function testSetOverrideAndAppend()
    {
        $d = new Dotable(['one' => ['two' => 1]]);
        $d->set('one', ['two' => 2, 'other' => 3]);
        $this->assertEquals(['one' => ['two' => 2, 'other' => 3]], $d->getValues());
    }
    public function testSetOverrideByArray()
    {
        $d = new Dotable(['one' => ['two' => 1]]);
        $d->set('one', ['other' => 3]);
        $this->assertEquals(['one' => ['other' => 3]], $d->getValues());
    }
    public function testSetPathByDoubleDots()
    {
        $d = new Dotable(['one' => ['two' => ['three' => 1]]]);
        $d->set('one:two:three', 3);
        $this->assertEquals(['one' => ['two' => ['three' => 3]]], $d->getValues());
    }
    public function testGet()
    {
        $d = new Dotable(['one' => ['two' => ['three' => 1]]]);
        $this->assertEquals(['one' => ['two' => ['three' => 1]]], $d->get(null));
        $this->assertEquals(['two' => ['three' => 1]], $d->get('one'));
        $this->assertEquals(['three' => 1], $d->get('one.two'));
        $this->assertEquals(1, $d->get('one.two.three'));
        $this->assertEquals(false, $d->get('one.two.three.next', false));
    }
    public function testHave()
    {
        $d = new Dotable(['one' => ['two' => ['three' => 1]]]);
        $this->assertTrue($d->have('one'));
        $this->assertTrue($d->have('one.two'));
        $this->assertTrue($d->have('one.two.three'));
        $this->assertFalse($d->have('one.two.three.false'));
        $this->assertFalse($d->have('one.false.three'));
        $this->assertFalse($d->have('false'));
    }
}
