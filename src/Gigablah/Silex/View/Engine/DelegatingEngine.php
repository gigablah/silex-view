<?php

namespace Gigablah\Silex\View\Engine;

use Gigablah\Silex\View\Exception\RenderException;

/**
 * Delegates rendering using an EngineResolver.
 *
 * @author Chris Heng <bigblah@gmail.com>
 */
class DelegatingEngine implements EngineInterface
{
    protected $resolver;

    /**
     * Constructor.
     *
     * @param EngineResolverInterface $resolver
     */
    public function __construct(EngineResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * {@inheritDoc}
     */
    public function render($template, $data = null)
    {
        if (false === $engine = $this->resolver->resolve($template)) {
            throw new RenderException(sprintf('Could not resolve engine for template "%s"', $template));
        }

        return $engine->render($template, $data);
    }

    /**
     * {@inheritDoc}
     */
    public function supports($template, $type = null)
    {
        return false === $this->resolver->resolve($template, $type) ? false : true;
    }
}
