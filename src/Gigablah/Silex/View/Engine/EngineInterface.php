<?php

namespace Gigablah\Silex\View\Engine;

/**
 * Interface that rendering engine adapters should implement.
 *
 * @author Chris Heng <bigblah@gmail.com>
 */
interface EngineInterface
{
    /**
     * Renders a template.
     *
     * @param mixed $template The template
     * @param mixed $data     The template data
     *
     * @return string The rendered template
     */
    public function render($template, $data = null);

    /**
     * Returns true if this engine supports the given template type.
     *
     * @param mixed  $template A template
     * @param string $type     The template type
     *
     * @return Boolean true if this engine supports the given template, false otherwise
     */
    public function supports($template, $type = null);
}
