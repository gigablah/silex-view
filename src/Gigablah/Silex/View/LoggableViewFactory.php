<?php

namespace Gigablah\Silex\View;

use Gigablah\Silex\View\Engine\EngineInterface;
use Gigablah\Silex\View\Logger\ViewLoggerInterface;

/**
 * Factory for creating loggable view objects.
 *
 * @author Chris Heng <bigblah@gmail.com>
 */
class LoggableViewFactory extends ViewFactory
{
    protected $logger;

    /**
     * Create a loggable view factory.
     *
     * @param EngineInterface     $engine The rendering engine
     * @param ViewLoggerInterface $logger View logger for debugging
     */
    public function __construct(EngineInterface $engine, ViewLoggerInterface $logger)
    {
        parent::__construct($engine);

        $this->logger = $logger;
    }

    /**
     * Create loggable view instances.
     *
     * @param mixed $template
     * @param mixed $data
     *
     * @return ViewInterface
     */
    public function create($template, $data = array())
    {
        if ($template instanceof LoggableView) {
            return $template->with($data);
        }

        $view = new LoggableView($template, $data, $this->engine, $this->sharedBag, $this->exceptionBag);
        $view->setLogger($this->logger);

        return $view;
    }
}
