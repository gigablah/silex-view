<?php

namespace Gigablah\Silex\View\Engine;

use Gigablah\Silex\View\ViewInterface;

/**
 * Raw PHP adapter.
 *
 * @author Chris Heng <bigblah@gmail.com>
 */
class PhpEngine implements EngineInterface
{
    /**
     * {@inheritDoc}
     */
    public function render($template, $data = null)
    {
        $data = $data instanceof \ArrayObject ? $data->getArrayCopy() : (array) $data;

        extract($data);

        ob_start();
        ob_implicit_flush(0);

        try {
            require $template;
        } catch (\Exception $exception) {
            ob_end_clean();
            throw $exception;
        }

        return ob_get_clean();
    }

    /**
     * {@inheritDoc}
     */
    public function supports($template, $type = null)
    {
        return in_array($type ?: pathinfo($template, PATHINFO_EXTENSION), array('php'));
    }
}
