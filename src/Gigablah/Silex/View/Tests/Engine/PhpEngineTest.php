<?php

namespace Gigablah\Silex\View\Tests\Engine;

use Gigablah\Silex\View\Engine\PhpEngine;

class PhpEngineTest extends \PHPUnit_Framework_TestCase
{
    private $engine;
    private $template;

    protected function setUp()
    {
        $this->engine = new PhpEngine();
        $this->template = dirname(__DIR__).'/Fixtures/foobar.php';
    }

    public function testRender()
    {
        $content = $this->engine->render($this->template, array('title' => 'Foo'));

        $this->assertEquals('<h1>Foo</h1>', $content);
    }

    /**
     * @expectedException \Exception
     */
    public function testRenderWithException()
    {
        $content = $this->engine->render(dirname(__DIR__).'/Fixtures/foobar.invalid.php');
    }

    public function testSupports()
    {
        $this->assertTrue($this->engine->supports($this->template, 'php'));
        $this->assertTrue($this->engine->supports($this->template));
        $this->assertFalse($this->engine->supports(''));
    }
}
