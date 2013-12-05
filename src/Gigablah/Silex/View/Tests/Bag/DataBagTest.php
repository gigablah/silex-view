<?php

namespace Gigablah\Silex\View\Tests\Bag;

use Gigablah\Silex\View\Bag\DataBag;

class DataBagTest extends \PHPUnit_Framework_TestCase
{
    private $bag;

    protected function setUp()
    {
        $this->bag = new DataBag(array('foo' => 'bar'));
    }

    public function testAll()
    {
        $this->assertEquals(array('foo' => 'bar'), $this->bag->all());
    }

    public function testKeys()
    {
        $this->assertEquals(array('foo'), $this->bag->keys());
    }

    public function testAdd()
    {
        $this->bag->add(array('bar' => 'foo'));

        $this->assertEquals(array('foo' => 'bar', 'bar' => 'foo'), $this->bag->all());
    }

    public function testGet()
    {
        $this->assertEquals('bar', $this->bag->get('foo'));
    }

    public function testSet()
    {
        $this->bag->set('foo', 'foo');

        $this->assertEquals('foo', $this->bag->get('foo'));
    }

    public function testHas()
    {
        $this->assertTrue($this->bag->has('foo'));
        $this->assertFalse($this->bag->has('bar'));

        $this->bag->set('bar', 'foo');
        $this->assertTrue($this->bag->has('bar'));
    }

    public function testRemove()
    {
        $this->bag->remove('foo');

        $this->assertFalse($this->bag->has('foo'));
    }

    public function testClear()
    {
        $this->bag->set('bar', 'foo');

        $this->bag->clear();

        $this->assertEquals(array(), $this->bag->all());
    }

    public function testGetIterator()
    {
        $this->assertInstanceOf('ArrayIterator', $this->bag->getIterator());

        $data = array();
        foreach ($this->bag as $key => $value) {
            $data[$key] = $value;
        }

        $this->assertEquals(array('foo' => 'bar'), $data);
    }

    public function testCount()
    {
        $this->assertSame(1, $this->bag->count());
        $this->assertSame(1, count($this->bag));

        $this->bag->set('bar', 'foo');

        $this->assertSame(2, $this->bag->count());
        $this->assertSame(2, count($this->bag));
    }
}
