<?php

namespace Gigablah\Silex\View\Logger;

use Gigablah\Silex\View\ViewInterface;

/**
 * Function signatures for logging and profiling views.
 *
 * @author Chris Heng <bigblah@gmail.com>
 */
interface ViewLoggerInterface
{
    /**
     * Logs the start of view rendering.
     *
     * @param ViewInterface $view
     */
    public function startRender(ViewInterface $view);

    /**
     * Logs the completion of view rendering.
     *
     * @param ViewInterface $view
     */
    public function stopRender(ViewInterface $view);

    /**
     * Returns all logged information.
     *
     * @return array
     */
    public function getViews();
}
