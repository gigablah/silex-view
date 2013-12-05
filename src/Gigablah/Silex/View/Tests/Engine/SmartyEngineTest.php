<?php

namespace Gigablah\Silex\View\Tests\Engine;

use Gigablah\Silex\View\Engine\SmartyEngine;

class SmartyEngineTest extends \PHPUnit_Framework_TestCase
{
    private $smarty;
    private $engine;
    private $template;

    protected function setUp()
    {
        $this->smarty = new \Smarty();
        $this->smarty->setTemplateDir(dirname(__DIR__).'/Fixtures');
        $this->smarty->setCompileDir('/tmp');
        $this->engine = new SmartyEngine($this->smarty);
        $this->template = 'foobar.tpl';
    }

    public function testRender()
    {
        $content = $this->engine->render($this->template, array('title' => 'Foo'));

        $this->assertEquals('<h1>Foo</h1>', $content);
    }

    public function testSupports()
    {
        $this->assertTrue($this->engine->supports($this->template, 'tpl'));
        $this->assertTrue($this->engine->supports($this->template));
        $this->assertFalse($this->engine->supports(''));
    }
}
