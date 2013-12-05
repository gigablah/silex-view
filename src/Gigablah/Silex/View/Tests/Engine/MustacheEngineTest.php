<?php

namespace Gigablah\Silex\View\Tests\Engine;

use Gigablah\Silex\View\Engine\MustacheEngine;

class MustacheEngineTest extends \PHPUnit_Framework_TestCase
{
    private $mustache;
    private $engine;
    private $template;

    protected function setUp()
    {
        $this->mustache = new \Mustache_Engine(array('loader' => new \Mustache_Loader_FilesystemLoader(dirname(__DIR__).'/Fixtures')));
        $this->engine = new MustacheEngine($this->mustache);
        $this->template = 'foobar.mustache';
    }

    public function testRender()
    {
        $content = $this->engine->render($this->template, array('title' => 'Foo'));

        $this->assertEquals('<h1>Foo</h1>', $content);
    }

    public function testSupports()
    {
        $this->assertTrue($this->engine->supports($this->template, 'mustache'));
        $this->assertTrue($this->engine->supports($this->template, 'ms'));
        $this->assertTrue($this->engine->supports($this->template));
        $this->assertFalse($this->engine->supports(''));
    }
}
