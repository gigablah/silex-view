<?php

namespace Gigablah\Silex\View\Logger;

use Gigablah\Silex\View\ViewInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Psr\Log\LoggerInterface;

/**
 * Default view logger that supports log collection and time profiling.
 *
 * @author Chris Heng <bigblah@gmail.com>
 */
class ViewLogger implements ViewLoggerInterface
{
    protected $logger;
    protected $stopwatch;
    protected $views;
    protected $events;

    /**
     * Constructor.
     *
     * @param LoggerInterface $logger    A LoggerInterface instance
     * @param Stopwatch       $stopwatch A Stopwatch instance
     */
    public function __construct(LoggerInterface $logger, Stopwatch $stopwatch = null)
    {
        $this->logger = $logger;
        $this->stopwatch = $stopwatch;
        $this->views = array();
        $this->events = array();
    }

    /**
     * {@inheritdoc}
     */
    public function startRender(ViewInterface $view)
    {
        if (null !== $this->stopwatch) {
            $id = spl_object_hash($view);
            $this->events[$id] = $this->stopwatch->start($view->getTemplate() ?: get_class($view), 'template');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function stopRender(ViewInterface $view)
    {
        $time = null;

        if (null !== $this->stopwatch) {
            $id = spl_object_hash($view);
            if (isset($this->events[$id])) {
                $this->events[$id]->stop($view->getTemplate() ?: get_class($view));
                $time = $this->events[$id]->getDuration();
                unset($this->events[$id]);
            }
        }

        $this->views[] = array(
            'view' => $view,
            'time' => $time
        );

        $this->logger->info(
            sprintf(
                '%s%s rendered%s%s',
                get_class($view),
                (null !== $template = $view->getTemplate()) ? sprintf(' "%s"', $template) : '',
                (null !== $engine = $view->getEngine()) ? sprintf(' with %s', get_class($engine)) : '',
                (null !== $time) ? sprintf(' in %sms', $time) : ''
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getViews()
    {
        return $this->views;
    }
}
