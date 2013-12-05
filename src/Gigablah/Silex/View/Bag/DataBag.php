<?php

namespace Gigablah\Silex\View\Bag;

/**
 * Generic container for key/value pairs.
 *
 * @author Chris Heng <bigblah@gmail.com>
 */
class DataBag implements \IteratorAggregate, \Countable
{
    protected $data = array();

    /**
     * Constructor.
     *
     * @param array $data An array of data
     */
    public function __construct(array $data = array())
    {
        $this->data = $data;
    }

    /**
     * Returns the view data.
     *
     * @return array An array of data
     */
    public function all()
    {
        return $this->data;
    }

    /**
     * Returns the data keys.
     *
     * @return array An array of data keys
     */
    public function keys()
    {
        return array_keys($this->data);
    }

    /**
     * Adds data.
     *
     * @param array $data An array of data
     */
    public function add(array $data = array())
    {
        $this->data = array_replace($this->data, $data);
    }

    /**
     * Returns data by key.
     *
     * @param string $key     The key
     * @param mixed  $default The default value if the data key does not exist
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return array_key_exists($key, $this->data) ? $this->data[$key] : $default;
    }

    /**
     * Sets data by key.
     *
     * @param string $key   The key
     * @param mixed  $value The value
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * Returns true if the data key is defined.
     *
     * @param string $key The key
     *
     * @return Boolean true if the key exists, false otherwise
     */
    public function has($key)
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * Removes a key.
     *
     * @param string $key The key
     */
    public function remove($key)
    {
        unset($this->data[$key]);
    }

    /**
     * Clears all data.
     */
    public function clear()
    {
        $this->data = array();
    }

    /**
     * Returns an iterator for data.
     *
     * @return \ArrayIterator An \ArrayIterator instance
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->data);
    }

    /**
     * Returns the number of data values.
     *
     * @return integer The number of data values
     */
    public function count()
    {
        return count($this->data);
    }
}
