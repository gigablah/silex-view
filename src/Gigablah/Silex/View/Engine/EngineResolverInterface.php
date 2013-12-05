<?php

namespace Gigablah\Silex\View\Engine;

/**
 * Interface for template engine resolvers.
 *
 * @author Chris Heng <bigblah@gmail.com>
 */
interface EngineResolverInterface
{
    /**
     * Returns an engine able to load and render the given template.
     *
     * @param mixed  $template A template
     * @param string $type     The template type
     *
     * @return EngineInterface|false An EngineInterface instance
     */
    public function resolve($template, $type = null);
}
