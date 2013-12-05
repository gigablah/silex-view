<?php

namespace Gigablah\Silex\View\Tests\Engine;

use Gigablah\Silex\View\Engine\EngineResolver;
use Gigablah\Silex\View\Engine\StringEngine;
use Gigablah\Silex\View\Engine\PhpEngine;

class EngineResolverTest extends \PHPUnit_Framework_TestCase
{
    private $resolver;

    protected function setUp()
    {
        $this->resolver = new EngineResolver(array(
            new PhpEngine()
        ));
    }

    public function testResolve()
    {
        $this->assertInstanceOf('Gigablah\\Silex\\View\\Engine\\PhpEngine', $this->resolver->resolve('foobar.php', 'php'));
        $this->assertInstanceOf('Gigablah\\Silex\\View\\Engine\\PhpEngine', $this->resolver->resolve('foobar.php'));
        $this->assertInstanceOf('Gigablah\\Silex\\View\\Engine\\PhpEngine', $this->resolver->resolve('foobar.html', 'php'));
        $this->assertInstanceOf('Gigablah\\Silex\\View\\Engine\\PhpEngine', $this->resolver->resolve('foobar', 'php'));
        $this->assertFalse($this->resolver->resolve('foobar.html', 'html'));
        $this->assertFalse($this->resolver->resolve('foobar.html'));
        $this->assertFalse($this->resolver->resolve('foobar.php', 'html'));

        $this->resolver->addEngine(new StringEngine());

        $this->assertInstanceOf('Gigablah\\Silex\\View\\Engine\\StringEngine', $this->resolver->resolve('foobar.html', 'html'));
        $this->assertInstanceOf('Gigablah\\Silex\\View\\Engine\\StringEngine', $this->resolver->resolve('foobar.html'));
        $this->assertInstanceOf('Gigablah\\Silex\\View\\Engine\\StringEngine', $this->resolver->resolve('foobar.php', 'html'));
        $this->assertInstanceOf('Gigablah\\Silex\\View\\Engine\\StringEngine', $this->resolver->resolve('foobar'));
    }
}
