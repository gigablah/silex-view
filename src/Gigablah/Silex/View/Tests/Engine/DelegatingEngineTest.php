<?php

namespace Gigablah\Silex\View\Tests\Engine;

use Gigablah\Silex\View\Engine\DelegatingEngine;
use Gigablah\Silex\View\Engine\MustacheEngine;
use Gigablah\Silex\View\Engine\SmartyEngine;
use Gigablah\Silex\View\Engine\TwigEngine;
use Gigablah\Silex\View\Engine\PhpEngine;
use Gigablah\Silex\View\Engine\StringEngine;
use Gigablah\Silex\View\Engine\EngineResolver;

class DelegatingEngineTest extends \PHPUnit_Framework_TestCase
{
    private $mustache;
    private $smarty;
    private $twig;
    private $resolver;
    private $engine;

    protected function setUp()
    {
        $this->mustache = new \Mustache_Engine(array('loader' => new \Mustache_Loader_FilesystemLoader(dirname(__DIR__).'/Fixtures')));
        $this->smarty = new \Smarty();
        $this->smarty->setTemplateDir(dirname(__DIR__).'/Fixtures');
        $this->smarty->setCompileDir('/tmp');
        $this->twig = new \Twig_Environment(new \Twig_Loader_Filesystem(dirname(__DIR__).'/Fixtures'));
        $this->resolver = new EngineResolver(array(
            new MustacheEngine($this->mustache),
            new SmartyEngine($this->smarty),
            new TwigEngine($this->twig),
            new PhpEngine(),
            new StringEngine()
        ));
        $this->engine = new DelegatingEngine($this->resolver);
    }

    public function testRenderMustache()
    {
        $content = $this->engine->render('foobar.mustache', array('title' => 'Foo'));

        $this->assertEquals('<h1>Foo</h1>', $content);
    }

    public function testRenderSmarty()
    {
        $content = $this->engine->render('foobar.tpl', array('title' => 'Foo'));

        $this->assertEquals('<h1>Foo</h1>', $content);
    }

    public function testRenderTwig()
    {
        $content = $this->engine->render('foobar.twig', array('title' => 'Foo'));

        $this->assertEquals('<h1>Foo</h1>', $content);
    }

    public function testRenderPhp()
    {
        $content = $this->engine->render(dirname(__DIR__).'/Fixtures/foobar.php', array('title' => 'Foo'));

        $this->assertEquals('<h1>Foo</h1>', $content);
    }

    public function testRenderString()
    {
        $content = $this->engine->render(dirname(__DIR__).'/Fixtures/foobar.html', array('{{title}}' => 'Foo'));

        $this->assertEquals('<h1>Foo</h1>', $content);
    }

    /**
     * @expectedException \Gigablah\Silex\View\Exception\RenderException
     */
    public function testRenderWithException()
    {
        $resolver = new EngineResolver(array());
        $engine = new DelegatingEngine($resolver);

        $content = $engine->render(dirname(__DIR__).'/Fixtures/foobar.invalid');
    }

    public function testSupports()
    {
        $this->assertTrue($this->engine->supports('', 'mustache'));
        $this->assertTrue($this->engine->supports('', 'ms'));
        $this->assertTrue($this->engine->supports('', 'tpl'));
        $this->assertTrue($this->engine->supports('', 'twig'));
        $this->assertTrue($this->engine->supports('', 'php'));
        $this->assertTrue($this->engine->supports('', 'html'));
        $this->assertTrue($this->engine->supports(''));
    }
}
