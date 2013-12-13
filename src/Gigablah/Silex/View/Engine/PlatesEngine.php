<?php

namespace Gigablah\Silex\View\Engine;

use League\Plates\Template;

/**
 * Plates adapter.
 *
 * @author Chris Heng <bigblah@gmail.com>
 */
class PlatesEngine implements EngineInterface
{
    protected $plates;

    /**
     * Constructor.
     *
     * @param Template $plates
     */
    public function __construct(Template $plates)
    {
        $this->plates = $plates;
    }

    /**
     * {@inheritDoc}
     */
    public function render($template, $data = null)
    {
        $data = $data instanceof \ArrayObject ? $data->getArrayCopy() : (array) $data;

        $this->plates->data($data);

        return $this->plates->render($template);
    }

    /**
     * {@inheritDoc}
     */
    public function supports($template, $type = null)
    {
        return in_array($type ?: pathinfo($template, PATHINFO_EXTENSION), array('php'));
    }
}
