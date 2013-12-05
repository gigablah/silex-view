<?php

namespace Gigablah\Silex\View\Engine;

/**
 * Mustache adapter.
 *
 * @author Chris Heng <bigblah@gmail.com>
 */
class MustacheEngine implements EngineInterface
{
    protected $mustache;

    /**
     * Constructor.
     *
     * @param \Mustache_Engine $mustache
     * @param array            $extensions
     */
    public function __construct(\Mustache_Engine $mustache)
    {
        $this->mustache = $mustache;
    }

    /**
     * {@inheritDoc}
     */
    public function render($template, $data = null)
    {
        return $this->mustache->loadTemplate($template)->render($data);
    }

    /**
     * {@inheritDoc}
     */
    public function supports($template, $type = null)
    {
        return in_array($type ?: pathinfo($template, PATHINFO_EXTENSION), array('ms', 'mustache'));
    }
}
