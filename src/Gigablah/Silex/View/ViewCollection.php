<?php

namespace Gigablah\Silex\View;

/**
 * An array-like collection of ViewInterface objects.
 *
 * @author Chris Heng <bigblah@gmail.com>
 */
class ViewCollection extends \ArrayObject implements ViewInterface
{
    /**
     * Create a new view collection.
     *
     * @param ViewInterface $view
     */
    public function __construct(ViewInterface $view)
    {
        parent::__construct(array($view));
    }

    /**
     * {@inheritDoc}
     */
    public function getTemplate()
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function getEngine()
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function render($context = array())
    {
        $content = '';

        foreach ($this as $view) {
            $content .= $view->render($context);
        }

        return $content;
    }

    /**
     * {@inheritDoc}
     */
    public function nest(ViewInterface $view, $key = 'content')
    {
        parent::offsetSet(null, $view);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function wrap(ViewInterface $view, $key = 'content')
    {
        $view[$key] = $this;

        return $view;
    }

    /**
     * {@inheritDoc}
     */
    public function with($context)
    {
        foreach ($this as $view) {
            $view->with($context);
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function inherit($context)
    {
        foreach ($this as $view) {
            $view->inherit($context);
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function share($context)
    {
        $view = parent::offsetGet(0);

        return $view->share($context);
    }

    /**
     * {@inheritDoc}
     */
    public function globals()
    {
        $view = parent::offsetGet(0);

        return $view->globals();
    }

    /**
     * {@inheritDoc}
     */
    public function all()
    {
        $context = array();

        foreach ($this as $view) {
            $context[] = $view->all();
        }

        return $context;
    }

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * {@inheritDoc}
     */
    public function toArray()
    {
        $content = array();

        foreach ($this as $view) {
            $content[] = $view->toArray();
        }

        return $content;
    }

    /**
     * {@inheritDoc}
     */
    public function getArrayCopy()
    {
        $content = array();

        foreach ($this as $view) {
            $content[] = $view->getArrayCopy();
        }

        return $content;
    }

    /**
     * Returns whether the requested index exists.
     *
     * @param string $id The unique identifier for the parameter or object
     *
     * @return Boolean True if the requested index exists, false otherwise
     */
    public function offsetExists($id)
    {
        foreach ($this as $view) {
            if ($view->offsetExists($id)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Gets a parameter or an object.
     *
     * @param string $id The unique identifier for the parameter or object
     *
     * @return mixed The value of the parameter or an object
     */
    public function offsetGet($id)
    {
        foreach ($this as $view) {
            if ($view->offsetExists($id)) {
                return $view->offsetGet($id);
            }
        }

        return null;
    }

    /**
     * Sets a parameter or an object.
     *
     * @param string $id    The unique identifier for the parameter or object
     * @param mixed  $value The value of the parameter or object
     */
    public function offsetSet($id, $value)
    {
        foreach ($this as $view) {
            $view->offsetSet($id, $value);
        }
    }

    /**
     * Unsets a parameter or an object.
     *
     * @param string $id The unique identifier for the parameter or object
     */
    public function offsetUnset($id)
    {
        foreach ($this as $view) {
            $view->offsetUnset($id);
        }
    }
}
