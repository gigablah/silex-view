<?php

namespace Gigablah\Silex\View;

use Gigablah\Silex\View\Logger\ViewLoggerInterface;

/**
 * View with logging functionality.
 *
 * @author Chris Heng <bigblah@gmail.com>
 */
class LoggableView extends View
{
    protected $logger;

    public function setLogger(ViewLoggerInterface $logger = null)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function render($context = array())
    {
        if (null === $this->logger) {
            return parent::render($context);
        }

        $this->logger->startRender($this);
        $content = parent::render($context);
        $this->logger->stopRender($this);

        return $content;
    }
}
