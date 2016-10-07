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
        $this->assertEquals(['one' => 1], $d->toArray());
    }

    public function testSetOverride()
    {
        $d = new Dotable(['one' => 1]);
        $d->set('one', 2);
        $this->assertEquals(['one' => 2], $d->toArray());
    }

    public function testSetPath()
    {
        $d = new Dotable(['one' => ['two' => 1]]);
        $d->set('one.two', 2);
        $this->assertEquals(['one' => ['two' => 2]], $d->toArray());

        $d->set('three.four.five', 1);
        $this->assertEquals(['one' => ['two' => 2], 'three' => ['four' => ['five' => 1]]], $d->toArray());

        $d->set('', ['one' => 'two']);
        $this->assertEquals(['one' => 'two'], $d->toArray());
    }

    public function testPathAppend()
    {
        $d = new Dotable(['one' => ['two' => 1]]);
        $d->set('one.other', 1);
        $this->assertEquals(['one' => ['two' => 1, 'other' => 1]], $d->toArray());
    }

    public function testSetAppend()
    {
        $d = new Dotable(['one' => ['two' => 1]]);
        $d->set('two', 2);
        $this->assertEquals(['one' => ['two' => 1], 'two' => 2], $d->toArray());
    }

    public function testSetAppendArray()
    {
        $d = new Dotable(['one' => ['two' => 1]]);
        $d->set('one', ['two' => 2]);
        $this->assertEquals(['one' => ['two' => 2]], $d->toArray());
    }

    public function testSetOverrideAndAppend()
    {
        $d = new Dotable(['one' => ['two' => 1]]);
        $d->set('one', ['two' => 2, 'other' => 3]);
        $this->assertEquals(['one' => ['two' => 2, 'other' => 3]], $d->toArray());
    }

    public function testSetOverrideByArray()
    {
        $d = new Dotable(['one' => ['two' => 1]]);
        $d->set('one', ['other' => 3]);
        $this->assertEquals(['one' => ['other' => 3]], $d->toArray());
    }

    public function testGet()
    {
        $d = new Dotable(['one' => ['two' => ['three' => 1]]]);
        $this->assertEquals(['one' => ['two' => ['three' => 1]]], $d->get());
        $this->assertEquals(['two' => ['three' => 1]], $d->get('one'));
        $this->assertEquals(['three' => 1], $d->get('one.two'));
        $this->assertEquals(1, $d->get('one.two.three'));
        $this->assertEquals(false, $d->get('one.two.three.next', false));
    }

    public function testHas()
    {
        $d = new Dotable(['one' => ['two' => ['three' => 1]]]);
        $this->assertTrue($d->has('one'));
        $this->assertTrue($d->has('one.two'));
        $this->assertTrue($d->has('one.two.three'));
        $this->assertFalse($d->has('one.two.three.false'));
        $this->assertFalse($d->has('one.false.three'));
        $this->assertFalse($d->has('false'));
    }

    public function testForget()
    {
        $d = new Dotable(['one' => ['two' => ['three' => 1]]]);
        $d->forget('one.two.three');
        $this->assertEquals(['two' => []], $d->get('one'));
        $d->forget('one.two');
        $this->assertEquals([], $d->get('one'));
    }

    public function testPrepend()
    {
        $d = new Dotable(['one' => ['two' => [2, 3]]]);
        $this->assertEquals(['one' => ['two' => [2, 3]]], $d->get());
        $d->prepend('one.two', 1);
        $this->assertEquals(['one' => ['two' => [1, 2, 3]]], $d->get());
    }

    public function testAppend()
    {
        $d = new Dotable(['one' => ['two' => [2, 3]]]);
        $this->assertEquals(['one' => ['two' => [2, 3]]], $d->get());
        $d->append('one.two', 4);
        $this->assertEquals(['one' => ['two' => [2, 3, 4]]], $d->get());
    }

    public function testMerge()
    {
        $d = new Dotable(['one' => 'two']);
        $d->merge('', ['one' => 'three']);
        $this->assertEquals(['one' => 'three'], $d->get());
    }

    public function testMergeDeep()
    {
        $d = new Dotable(['one' => ['two' => ['three', 'four']]]);
        $d->merge('', ['one' => ['two' => ['five', 'six']]]);
        $this->assertEquals(['one' => ['two' => ['three', 'four', 'five', 'six']]], $d->get());
    }

    public function testMergeIndexDeep()
    {
        $d = new Dotable(['one' => ['two' => [0 => 'three', 1 => 'four']]]);
        $d->merge('', ['one' => ['two' => [0 => 'five', 1 => 'six', 2 => 'severn']]]);
        $this->assertEquals(['one' => ['two' => ['three', 'four', 'five', 'six', 'severn']]], $d->get());
    }

    public function testMergeKeyedDeep()
    {
        $d = new Dotable(['one' => ['two' => ['three' => 'three', 'four' => 'four']]]);
        $d->merge('', ['one' => ['two' => ['five' => 'five', 'three' => 'six']]]);
        $this->assertEquals(['one' => ['two' => ['three' => 'six', 'four' => 'four', 'five' => 'five']]], $d->get());
    }

    public function testToJson()
    {
        $d = new Dotable(['one' => ['two' => ['three' => 1]]]);
        $this->assertEquals(['one' => ['two' => ['three' => 1]]], $d->jsonSerialize());
        $this->assertEquals('{"one":{"two":{"three":1}}}', json_encode($d));
    }

    public function testToString()
    {
        $d = new Dotable(['one' => ['two' => ['three' => 1]]]);
        $this->assertEquals('{"one":{"two":{"three":1}}}', (string) $d);
    }

    public function testArrayAccess()
    {
        $d = new Dotable(['one' => ['two' => ['three' => 1]]]);
        $this->assertEquals(['two' => ['three' => 1]], $d['one']);
    }

    public function testArraySet()
    {
        $d = new Dotable(['one' => ['two' => ['three' => 1]]]);
        $d['one.two.three'] = 2;
        $this->assertEquals(['two' => ['three' => 2]], $d['one']);
    }

    public function testArrayExists()
    {
        $d = new Dotable(['one' => ['two' => ['three' => 1]]]);
        $this->assertTrue(isset($d['one']));
        $this->assertTrue(isset($d['one.two']));
        $this->assertTrue(isset($d['one.two.three']));
        $this->assertFalse(isset($d['one.two.three.false']));
        $this->assertFalse(isset($d['one.false.three']));
        $this->assertFalse(isset($d['two']));
    }

    public function testArrayUnset()
    {
        $d = new Dotable(['one' => ['two' => ['three' => 1]]]);
        unset($d['one.two.three']);
        $this->assertEquals(['two' => []], $d['one']);
        unset($d['one.two']);
        $this->assertEquals([], $d['one']);
    }

    public function testArrayForeachIterator()
    {
        $d = new Dotable(['one' => 1, 'two' => 2]);

        $string = '';

        foreach($d as $key => $value){
            $string .= $key . $value;
        }

        $this->assertEquals('one1two2', $string);
    }

    public function testCount()
    {
        $d = new Dotable(['one' => 1, 'two' => 2]);

        $this->assertCount(2, $d);
    }
}
