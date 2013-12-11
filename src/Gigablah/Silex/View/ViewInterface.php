<?php

namespace Gigablah\Silex\View;

/**
 * ViewInterface provides the basic signature of all View objects.
 *
 * To return the view context without globals, or nested views: $view->all()
 * To return the complete view data including globals: $view->getArrayCopy()
 * To convert the view to an array, including nested views: $view->toArray()
 *
 * @author Chris Heng <bigblah@gmail.com>
 */
interface ViewInterface
{
    /**
     * Returns the template.
     *
     * @return string
     */
    public function getTemplate();

    /**
     * Returns the rendering engine.
     *
     * @return string
     */
    public function getEngine();

    /**
     * Renders the view output.
     *
     * @param mixed $context
     *
     * @return string
     */
    public function render($context = array());

    /**
     * Insert another view as a data element.
     *
     * @param ViewInterface $view
     * @param string        $key
     *
     * @return ViewInterface
     */
    public function nest(ViewInterface $view, $key = 'content');

    /**
     * Wrap the current view with another view.
     *
     * @param ViewInterface $view
     * @param string        $key
     *
     * @return ViewInterface
     */
    public function wrap(ViewInterface $view, $key = 'content');

    /**
     * Insert view data.
     *
     * @param mixed $context
     *
     * @return ViewInterface
     */
    public function with($context);

    /**
     * Inherit parent data. For internal use.
     *
     * @param mixed $context
     *
     * @return ViewInterface
     */
    public function inherit($context);

    /**
     * Apply global values across all views.
     *
     * @param mixed $context
     *
     * @return ViewInterface
     */
    public function share($context);

    /**
     * Dump all global values.
     *
     * @return array
     */
    public function globals();

    /**
     * Returns the view data.
     *
     * @return array
     */
    public function all();

    /**
     * Renders the view output.
     *
     * @return string
     */
    public function __toString();

    /**
     * Converts all view data to an array, including nested views.
     *
     * @return array
     */
    public function toArray();
}
