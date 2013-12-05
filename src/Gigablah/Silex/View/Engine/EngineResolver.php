<?php

namespace Gigablah\Silex\View\Engine;

/**
 * Selects an appropriate rendering engine for a given template.
 *
 * @author Chris Heng <bigblah@gmail.com>
 */
class EngineResolver implements EngineResolverInterface
{
    protected $engines;

    /**
     * Constructor.
     *
     * @param EngineInterface[] $engines An array of engines
     */
    public function __construct(array $engines = array())
    {
        $this->engines = array();
        foreach ($engines as $engine) {
            $this->addEngine($engine);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function resolve($template, $type = null)
    {
        foreach ($this->getEngines() as $engine) {
            if ($engine->supports($template, $type)) {
                return $engine;
            }
        }

        return false;
    }

    /**
     * Adds an engine.
     *
     * @param EngineInterface $engine An EngineInterface instance
     */
    public function addEngine(EngineInterface $engine)
    {
        $this->engines[] = $engine;
    }

    /**
     * Returns the registered engines.
     *
     * @return EngineInterface[] An array of EngineInterface instances
     */
    public function getEngines()
    {
        return $this->engines;
    }
}
