<?php

namespace Gigablah\Silex\View\Tests;

use Gigablah\Silex\View\View;
use Gigablah\Silex\View\Bag\DataBag;
use Gigablah\Silex\View\Bag\ExceptionBag;
use Gigablah\Silex\View\Engine\DelegatingEngine;
use Gigablah\Silex\View\Engine\MustacheEngine;
use Gigablah\Silex\View\Engine\SmartyEngine;
use Gigablah\Silex\View\Engine\TwigEngine;
use Gigablah\Silex\View\Engine\PhpEngine;
use Gigablah\Silex\View\Engine\StringEngine;
use Gigablah\Silex\View\Engine\EngineResolver;

class ViewTest extends \PHPUnit_Framework_TestCase
{
    private $sharedBag;
    private $exceptionBag;
    private $mustache;
    private $smarty;
    private $twig;
    private $resolver;
    private $engine;
    private $view;

    protected function setUp()
    {
        $this->sharedBag = new DataBag();
        $this->exceptionBag = new ExceptionBag();
        $this->mustache = new \Mustache_Engine(array('loader' => new \Mustache_Loader_FilesystemLoader(__DIR__.'/Fixtures')));
        $this->smarty = new \Smarty();
        $this->smarty->setTemplateDir(__DIR__.'/Fixtures');
        $this->smarty->setCompileDir('/tmp');
        $this->twig = new \Twig_Environment(new \Twig_Loader_Filesystem(__DIR__.'/Fixtures'));
        $this->resolver = new EngineResolver(array(
            new MustacheEngine($this->mustache),
            new SmartyEngine($this->smarty),
            new TwigEngine($this->twig),
            new PhpEngine(),
            new StringEngine()
        ));
        $this->engine = new DelegatingEngine($this->resolver);
        $this->view = new View(__DIR__.'/Fixtures/foobar.html', array(), $this->engine, $this->sharedBag, $this->exceptionBag);
    }

    public function testGetTemplate()
    {
        $this->assertEquals(__DIR__.'/Fixtures/foobar.html', $this->view->getTemplate());
    }

    public function testGetEngine()
    {
        $this->assertEquals($this->engine, $this->view->getEngine());
    }

    public function testRender()
    {
        $content = $this->view->render(array('{{title}}' => 'Foo'));

        $this->assertEquals('<h1>Foo</h1>', $content);
    }

    public function testRenderMultipleNested()
    {
        $child = clone $this->view;
        $grandchild = clone $this->view;
        $content = $this->view->render(
            array(
                '{{title}}' => $child->nest(
                    $grandchild->with(
                        array(
                            '{{title}}' => 'Foo'
                        )
                    ), '{{title}}'
                )
            )
        );

        $this->assertEquals('<h1><h1><h1>Foo</h1></h1></h1>', $content);
    }

    public function testRenderWithException()
    {
        $view = new View(__DIR__.'/Fixtures/foobar.invalid.twig', array(), $this->engine, $this->sharedBag, $this->exceptionBag);

        $this->assertSame(0, $this->exceptionBag->count());

        $view->render();

        $this->assertSame(1, $this->exceptionBag->count());
    }

    public function testNest()
    {
        $view = new View(__DIR__.'/Fixtures/foobar.html', array('{{title}}' => 'bar'), $this->engine);

        $this->view->nest($view, '{{title}}');

        $this->assertEquals($view, $this->view['{{title}}']);
        $this->assertEquals('<h1><h1>bar</h1></h1>', $this->view->render());
    }

    public function testWrap()
    {
        $view = new View(__DIR__.'/Fixtures/foobar.html', array(), $this->engine);
        $this->view->with(array('{{title}}' => 'bar'));
        $view = $this->view->wrap($view, '{{title}}');

        $this->assertEquals($this->view, $view['{{title}}']);
        $this->assertEquals('<h1><h1>bar</h1></h1>', $view->render());
    }

    public function testWith()
    {
        $content = $this->view->with(array('{{title}}' => 'Bar'))->render();

        $this->assertEquals('<h1>Bar</h1>', $content);
    }

    public function testShare()
    {
        $view = new View(__DIR__.'/Fixtures/foobar.html', array(), $this->engine, $this->sharedBag);

        $this->assertNull($view['foo']);
        $this->assertNull($view['bar']);
        $this->assertNull($this->view['foo']);
        $this->assertNull($this->view['bar']);

        $view->share(array('foo' => 'bar'));

        $this->assertEquals('bar', $view['foo']);
        $this->assertEquals('bar', $this->view['foo']);

        $this->view->share(array('bar' => 'foo'));

        $this->assertEquals('foo', $this->view['bar']);
        $this->assertEquals('foo', $view['bar']);
    }

    public function testGlobals()
    {
        $this->assertEquals(array(), $this->view->globals());

        $this->view->share(array('foo' => 'bar'));

        $this->assertEquals(array('foo' => 'bar'), $this->view->globals());
    }

    public function testAll()
    {
        $this->assertEquals(array(), $this->view->all());

        $this->view->with(array('foo' => 'bar'));

        $this->assertEquals(array('foo' => 'bar'), $this->view->all());
    }

    public function testToString()
    {
        $view = $this->view->with(array('{{title}}' => 'Bar'));

        $this->assertEquals('<h1>Bar</h1>', (string) $view);
    }

    public function testToArray()
    {
        $template = __DIR__.'/Fixtures/foobar.html';

        $this->assertEquals(array('_template' => $template), $this->view->toArray());

        $this->view->with(array('foo' => new View($template, array('bar' => 'foo'), $this->engine)));

        $this->assertEquals(array('foo' => array('bar' => 'foo', '_template' => $template), '_template' => $template), $this->view->toArray());
    }

    public function testGetArrayCopy()
    {
        $this->assertEquals(array(), $this->view->all());

        $this->view->with(array('foo' => 'bar'));

        $this->assertEquals(array('foo' => 'bar'), $this->view->getArrayCopy());

        $this->view->share(array('foo' => 'foo', 'bar' => 'foo'));

        $this->assertEquals(array('foo' => 'bar', 'bar' => 'foo'), $this->view->getArrayCopy());
    }

    public function testArrayAccess()
    {
        $this->assertFalse(isset($this->view['foo']));
        $this->assertFalse($this->view->offsetExists('foo'));
        $this->assertFalse(isset($this->view['bar']));
        $this->assertFalse($this->view->offsetExists('bar'));

        $this->view['foo'] = 'bar';

        $this->assertTrue(isset($this->view['foo']));
        $this->assertTrue($this->view->offsetExists('foo'));
        $this->assertEquals('bar', $this->view->offsetGet('foo'));

        $this->view->offsetSet('bar', 'baz');

        $this->assertTrue(isset($this->view['bar']));
        $this->assertTrue($this->view->offsetExists('bar'));
        $this->assertEquals('baz', $this->view->offsetGet('bar'));

        unset($this->view['foo']);

        $this->assertFalse(isset($this->view['foo']));
        $this->assertNull($this->view['foo']);

        $this->view->offsetUnset('bar');

        $this->assertFalse(isset($this->view['bar']));
        $this->assertNull($this->view['bar']);
    }

    public function testRenderCompositeViews()
    {
        $htmlView = new View(__DIR__.'/Fixtures/foobar.html', array(), $this->engine, $this->sharedBag, $this->exceptionBag);
        $mustacheView = new View('foobar.mustache', array('title' => $htmlView), $this->engine, $this->sharedBag, $this->exceptionBag);
        $smartyView = new View('foobar.tpl', array('title' => $mustacheView), $this->engine, $this->sharedBag, $this->exceptionBag);
        $twigView = new View('foobar.twig', array('title' => $smartyView), $this->engine, $this->sharedBag, $this->exceptionBag);
        $phpView = new View(__DIR__.'/Fixtures/foobar.php', array('title' => $twigView), $this->engine, $this->sharedBag, $this->exceptionBag);
        $phpView->share(array('{{title}}' => 'foo'));

        $this->assertEquals('<h1><h1><h1><h1><h1>foo</h1></h1></h1></h1></h1>', $phpView->render());
    }

    public function testContextInheritance()
    {
        $htmlView = new View(__DIR__.'/Fixtures/foobar.html', array(), $this->engine, $this->sharedBag, $this->exceptionBag);
        $mustacheView = new View('foobar.mustache', array('title' => $htmlView), $this->engine, $this->sharedBag, $this->exceptionBag);
        $smartyView = new View('foobar.tpl', array('title' => $mustacheView), $this->engine, $this->sharedBag, $this->exceptionBag);
        $twigView = new View('foobar.twig', array('title' => $smartyView), $this->engine, $this->sharedBag, $this->exceptionBag);
        $phpView = new View(__DIR__.'/Fixtures/foobar.php', array('title' => $twigView), $this->engine, $this->sharedBag, $this->exceptionBag);
        $phpView['{{title}}'] = 'foo';

        $this->assertEquals('<h1><h1><h1><h1><h1>foo</h1></h1></h1></h1></h1>', $phpView->render());
    }
}
