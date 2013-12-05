<?php

namespace Gigablah\Silex\View\Tests\Engine;

use Gigablah\Silex\View\Engine\TwigEngine;

class TwigEngineTest extends \PHPUnit_Framework_TestCase
{
    private $twig;
    private $engine;
    private $template;

    protected function setUp()
    {
        $this->twig = new \Twig_Environment(new \Twig_Loader_Filesystem(dirname(__DIR__).'/Fixtures'));
        $this->engine = new TwigEngine($this->twig);
        $this->template = 'foobar.twig';
    }

    public function testRender()
    {
        $content = $this->engine->render($this->template, array('title' => 'Foo'));

        $this->assertEquals('<h1>Foo</h1>', $content);
    }

    public function testSupports()
    {
        $this->assertTrue($this->engine->supports($this->template, 'twig'));
        $this->assertTrue($this->engine->supports($this->template));
        $this->assertFalse($this->engine->supports(''));
    }
}
