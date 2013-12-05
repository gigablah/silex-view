<?php

namespace Gigablah\Silex\View\Engine;

use Aura\View\Template;

/**
 * Aura.View adapter.
 *
 * @author Chris Heng <bigblah@gmail.com>
 */
class AuraEngine implements EngineInterface
{
    protected $aura;

    /**
     * Constructor.
     *
     * @param Template $aura
     */
    public function __construct(Template $aura)
    {
        $this->aura = $aura;
    }

    /**
     * {@inheritDoc}
     */
    public function render($template, $data = null)
    {
        $data = $data instanceof \ArrayObject ? $data->getArrayCopy() : (array) $data;

        $this->aura->setData($data);

        return $this->aura->fetch($template);
    }

    /**
     * {@inheritDoc}
     */
    public function supports($template, $type = null)
    {
        return in_array($type ?: pathinfo($template, PATHINFO_EXTENSION), array('php'));
    }
}
