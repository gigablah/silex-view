<?php

namespace Gigablah\Silex\View\Engine;

use Silex\Application;

/**
 * Lazily selects an appropriate rendering engine for a given template.
 *
 * @author Chris Heng <bigblah@gmail.com>
 */
class LazyEngineResolver implements EngineResolverInterface
{
    protected $app;
    protected $mapping;
    protected $type;

    /**
     * Constructor.
     *
     * @param Application $app     An Application instance
     * @param array       $mapping An association of types to engine identifiers
     * @param string      $type    Default template type to use
     */
    public function __construct(Application $app, array $mapping = array(), $type = null)
    {
        $this->app = $app;
        $this->mapping = $mapping;
        $this->type = $type;
    }

    /**
     * {@inheritDoc}
     */
    public function resolve($template, $type = null)
    {
        $type = $type ?: pathinfo($template, PATHINFO_EXTENSION) ?: $this->type;

        if (isset($this->mapping[$type])) {
            $identifier = $this->mapping[$type];

            return $this->app[$identifier];
        }

        return false;
    }

    /**
     * Adds an engine identifier for a particular template type.
     *
     * @param string $type      A template type
     * @param string $identifer An engine identifier
     */
    public function addMapping($type, $identifier)
    {
        $this->mapping[$type] = $identifier;
    }
}
