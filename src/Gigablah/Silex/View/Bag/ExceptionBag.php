<?php

namespace Gigablah\Silex\View\Bag;

use Gigablah\Silex\View\Exception\RenderException;

/**
 * Container for holding render exceptions.
 *
 * @author Chris Heng <bigblah@gmail.com>
 */
class ExceptionBag implements \IteratorAggregate, \Countable
{
    protected $exceptions = array();

    /**
     * Returns all exceptions.
     *
     * @return array An array of exceptions
     */
    public function all()
    {
        return $this->exceptions;
    }

    /**
     * Adds an exception.
     *
     * @param \Exception $exception An exception
     */
    public function add(\Exception $exception)
    {
        $this->exceptions[] = $exception;
    }

    /**
     * Returns the latest exception.
     *
     * @return \Exception|null
     */
    public function pop()
    {
        if (!$this->count()) {
            return null;
        }

        return array_pop($this->exceptions);
    }

    /**
     * Clears all exceptions.
     */
    public function clear()
    {
        $this->exceptions = array();
    }

    /**
     * Returns an iterator for the exception array.
     *
     * @return \ArrayIterator An \ArrayIterator instance
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->exceptions);
    }

    /**
     * Returns the number of exceptions
     *
     * @return integer The number of exceptions
     */
    public function count()
    {
        return count($this->exceptions);
    }
}
