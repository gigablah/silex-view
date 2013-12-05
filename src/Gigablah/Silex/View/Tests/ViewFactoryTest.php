<?php

namespace Gigablah\Silex\View\Tests;

use Gigablah\Silex\View\View;
use Gigablah\Silex\View\ViewFactory;
use Gigablah\Silex\View\Engine\StringEngine;

class ViewFactoryTest extends \PHPUnit_Framework_TestCase
{
    private $engine;
    private $factory;

    protected function setUp()
    {
        $this->engine = new StringEngine();
        $this->factory = new ViewFactory($this->engine);
    }

    public function testGetSharedBag()
    {
        $this->assertInstanceOf('Gigablah\\Silex\\View\\Bag\\DataBag', $this->factory->getSharedBag());
    }

    public function testGetExceptionBag()
    {
        $this->assertInstanceOf('Gigablah\\Silex\\View\\Bag\\ExceptionBag', $this->factory->getExceptionBag());
    }

    public function testCreate()
    {
        $view = $this->factory->create('foobar.html', array('foo' => 'bar'));

        $this->assertInstanceOf('Gigablah\\Silex\\View\\ViewInterface', $view);
        $this->assertEquals('bar', $view['foo']);

        $view = $this->factory->create($view, array('bar' => 'foo'));

        $this->assertEquals('foo', $view['bar']);
    }
}
