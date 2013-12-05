<?php

namespace Gigablah\Silex\View\Tests\Engine;

use Gigablah\Silex\View\Engine\LazyEngineResolver;
use Gigablah\Silex\View\Engine\StringEngine;
use Gigablah\Silex\View\Engine\PhpEngine;
use Silex\Application;

class LazyEngineResolverTest extends \PHPUnit_Framework_TestCase
{
    private $app;
    private $resolver;

    protected function setUp()
    {
        $this->app = new Application();
        $this->app['view.engine.string'] = new StringEngine();
        $this->app['view.engine.php'] = new PhpEngine();
        $this->resolver = new LazyEngineResolver($this->app, array(
            'html' => 'view.engine.string'
        ), 'html');
    }

    public function testResolve()
    {
        $this->assertInstanceOf('Gigablah\\Silex\\View\\Engine\\StringEngine', $this->resolver->resolve('foobar.html', 'html'));
        $this->assertInstanceOf('Gigablah\\Silex\\View\\Engine\\StringEngine', $this->resolver->resolve('foobar.html'));
        $this->assertInstanceOf('Gigablah\\Silex\\View\\Engine\\StringEngine', $this->resolver->resolve('foobar.php', 'html'));
        $this->assertInstanceOf('Gigablah\\Silex\\View\\Engine\\StringEngine', $this->resolver->resolve('foobar'));
        $this->assertFalse($this->resolver->resolve('foobar.php', 'php'));
        $this->assertFalse($this->resolver->resolve('foobar.php'));
        $this->assertFalse($this->resolver->resolve('foobar.html', 'php'));

        $this->resolver->addMapping('php', 'view.engine.php');

        $this->assertInstanceOf('Gigablah\\Silex\\View\\Engine\\PhpEngine', $this->resolver->resolve('foobar.php', 'php'));
        $this->assertInstanceOf('Gigablah\\Silex\\View\\Engine\\PhpEngine', $this->resolver->resolve('foobar.php'));
        $this->assertInstanceOf('Gigablah\\Silex\\View\\Engine\\PhpEngine', $this->resolver->resolve('foobar.html', 'php'));
        $this->assertInstanceOf('Gigablah\\Silex\\View\\Engine\\PhpEngine', $this->resolver->resolve('foobar', 'php'));
    }
}
