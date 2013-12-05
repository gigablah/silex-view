<?php

namespace Gigablah\Silex\View\Tests;

use Gigablah\Silex\View\LoggableView;
use Gigablah\Silex\View\Engine\StringEngine;

class LoggableViewTest extends \PHPUnit_Framework_TestCase
{
    private $engine;
    private $view;

    protected function setUp()
    {
        $this->engine = new StringEngine();
        $this->logger = $this->getMockBuilder('Gigablah\\Silex\\View\\Logger\\ViewLogger')->disableOriginalConstructor()->getMock();
        $this->view = new LoggableView(__DIR__.'/Fixtures/foobar.html', array(), $this->engine);
    }

    public function testRender()
    {
        $this->logger->expects($this->once())->method('startRender')->with($this->equalTo($this->view));
        $this->logger->expects($this->once())->method('stopRender')->with($this->equalTo($this->view));

        $content = $this->view->render(array('{{title}}' => 'Foo'));

        $this->view->setLogger($this->logger);

        $content = $this->view->render(array('{{title}}' => 'Foo'));
    }
}
