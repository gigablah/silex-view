<?php

namespace Gigablah\Silex\View\Tests\Template;

use Gigablah\Silex\View\Template\TemplateResolver;
use Symfony\Component\HttpFoundation\Request;

class TemplateResolverTest extends \PHPUnit_Framework_TestCase
{
    private $request;

    protected function setUp()
    {
        $this->request = new Request();
    }

    public function testResolve()
    {
        $resolver = new TemplateResolver();

        $this->request->attributes->set('_route', 'home');

        $this->assertEquals('home', $resolver->resolve($this->request));

        $controllerResult = array('_template' => 'foo');

        $this->assertEquals('foo', $resolver->resolve($this->request, $controllerResult));

        $this->request->attributes->set('_template', 'bar');

        $this->assertEquals('bar', $resolver->resolve($this->request, $controllerResult));
    }

    public function testResolveWithDefaultType()
    {
        $resolver = new TemplateResolver('html');

        $this->request->attributes->set('_route', 'home');

        $this->assertEquals('home.html', $resolver->resolve($this->request));

        $controllerResult = array('_template' => 'foo');

        $this->assertEquals('foo.html', $resolver->resolve($this->request, $controllerResult));

        $this->request->attributes->set('_template', 'bar');

        $this->assertEquals('bar.html', $resolver->resolve($this->request, $controllerResult));
    }
}
