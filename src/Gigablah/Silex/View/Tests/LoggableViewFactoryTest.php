<?php

namespace Gigablah\Silex\View\Tests;

use Gigablah\Silex\View\View;
use Gigablah\Silex\View\LoggableViewFactory;
use Gigablah\Silex\View\Engine\StringEngine;

class LoggableViewFactoryTest extends \PHPUnit_Framework_TestCase
{
    private $engine;
    private $logger;
    private $factory;

    protected function setUp()
    {
        $this->engine = new StringEngine();
        $this->logger = $this->getMockBuilder('Gigablah\\Silex\\View\\Logger\\ViewLogger')->disableOriginalConstructor()->getMock();
        $this->factory = new LoggableViewFactory($this->engine, $this->logger);
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

        $this->assertInstanceOf('Gigablah\\Silex\\View\\LoggableView', $view);
        $this->assertEquals('bar', $view['foo']);

        $view = $this->factory->create($view, array('bar' => 'foo'));

        $this->assertEquals('foo', $view['bar']);
    }
}
