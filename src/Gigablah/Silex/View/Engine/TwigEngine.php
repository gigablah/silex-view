<?php

namespace Gigablah\Silex\View\Engine;

/**
 * Twig adapter.
 *
 * @author Chris Heng <bigblah@gmail.com>
 */
class TwigEngine implements EngineInterface
{
    protected $twig;

    /**
     * Constructor.
     *
     * @param \Twig_Environment $twig
     */
    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * {@inheritDoc}
     */
    public function render($template, $data = null)
    {
        $data = $data instanceof \ArrayObject ? $data->getArrayCopy() : (array) $data;

        return $this->twig->render($template, $data);
    }

    /**
     * {@inheritDoc}
     */
    public function supports($template, $type = null)
    {
        return in_array($type ?: pathinfo($template, PATHINFO_EXTENSION), array('twig'));
    }
}
