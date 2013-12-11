<?php

namespace Gigablah\Silex\View;

use Gigablah\Silex\View\Engine\EngineInterface;
use Gigablah\Silex\View\Bag\DataBag;
use Gigablah\Silex\View\Bag\ExceptionBag;

/**
 * Factory for creating view objects.
 *
 * @author Chris Heng <bigblah@gmail.com>
 */
class ViewFactory
{
    protected $engine;
    protected $sharedBag;
    protected $exceptionBag;

    /**
     * Create a view factory.
     *
     * @param EngineInterface $engine The rendering engine
     */
    public function __construct(EngineInterface $engine)
    {
        $this->engine = $engine;
        $this->sharedBag = new DataBag();
        $this->exceptionBag = new ExceptionBag();
    }

    /**
     * Sets the global context.
     *
     * @param mixed $context The global context
     *
     * @return ViewFactory
     */
    public function share($context)
    {
        foreach ((array) $context as $key => $value) {
            $this->sharedBag->set($key, $value);
        }

        return $this;
    }

    /**
     * Returns the shared data container.
     *
     * @return DataBag
     */
    public function getSharedBag()
    {
        return $this->sharedBag;
    }

    /**
     * Returns the shared exception container.
     *
     * @return ExceptionBag
     */
    public function getExceptionBag()
    {
        return $this->exceptionBag;
    }

    /**
     * Create new view instances.
     *
     * @param mixed $template
     * @param mixed $data
     *
     * @return ViewInterface
     */
    public function create($template, $data = array())
    {
        if ($template instanceof ViewInterface) {
            return $template->with($data);
        }

        return new View($template, $data, $this->engine, $this->sharedBag, $this->exceptionBag);
    }
}
