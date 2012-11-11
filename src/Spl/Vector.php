<?php

namespace Spl;

use ArrayAccess,
    IteratorAggregate;

class Vector implements IteratorAggregate, ArrayAccess, Collection {

    protected $array = array();

    /**
     * @param mixed,... $varargs
     * @throws TypeException
     */
    function __construct($varargs = NULL) {
        $this->array = func_get_args();
    }

    /**
     * @return void
     */
    function clear() {
        $this->array = array();
    }

    /**
     * @param $object
     *
     * @return bool
     * @throws TypeException when $object is not the correct type.
     */
    function contains($object) {
        return in_array($object, $this->array);
    }

    /**
     * @return bool
     */
    function isEmpty() {
        return count($this->array) === 0;
    }

    /**
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @param int $offset
     *
     * @return boolean
     */
    public function offsetExists($offset) {
        return $offset >= 0 && $offset < $this->count();
    }

    /**
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     *
     * @param int $offset
     *
     * @throws IndexException
     * @throws TypeException
     * @return mixed
     */
    public function offsetGet($offset) {
        return $this->get($offset);
    }

    /**
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     *
     * @param int $offset
     * @param mixed $value
     *
     * @throws IndexException
     * @throws TypeException
     * @return void
     */
    public function offsetSet($offset, $value) {
        if ($offset === NULL) {
            $this->append($value);
            return;
        }
        $this->set($offset, $value);
    }

    /**
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @param int $offset
     *
     * @return void
     */
    public function offsetUnset($offset) {
        $this->remove($offset);
    }

    /**
     * @link http://php.net/manual/en/countable.count.php
     * @return int
     */
    public function count() {
        return count($this->array);
    }

    /**
     * @param $item
     *
     * @return void
     * @throws TypeException when $item is not the correct type.
     */
    function append($item) {
        $this->array[] = $item;
    }

    /**
     * @param int $index
     *
     * @return mixed
     * @throws TypeException when $index is not an integer.
     * @throws IndexException when $index < 0 or $index >= count($this).
     */
    function get($index) {
        if (filter_var($index, FILTER_VALIDATE_INT) === FALSE) {
            throw new TypeException;
        }

        if (!$this->offsetExists($index)) {
            throw new IndexException;
        }

        return $this->array[$index];
    }

    /**
     * @param int $index
     * @param $item
     *
     * @return void
     * @throws TypeException when $index is not an integer or when $item is not the correct type.
     * @throws IndexException when $index < 0 or $index >= count($this).
     */
    function set($index, $item) {
        if (filter_var($index, FILTER_VALIDATE_INT) === FALSE) {
            throw new TypeException;
        }

        if (!$this->offsetExists($index)) {
            throw new IndexException;
        }

        $this->array[$index] = $item;
    }

    /**
     * @param int $index
     *
     * @throws TypeException when $index is not an integer.
     * @return void
     */
    function remove($index) {
        if (filter_var($index, FILTER_VALIDATE_INT) === FALSE) {
            throw new TypeException;
        }

        if (!$this->offsetExists($index)) {
            return;
        }

        array_splice($this->array, $index, 1);
    }

    /**
     * @param mixed $object
     *
     * @throws TypeException if $object is the incorrect type for the Vector
     * @return void
     */
    function removeItem($object) {
        $index = array_search($object, $this->array);
        if ($index === FALSE) {
            return;
        }
        array_splice($this->array, $index, 1);
    }

    /**
     * @param int $startIndex
     * @param int $numberOfItemsToExtract [optional] If not provided, it will extract all items after the $startIndex.
     *
     * @return Vector
     * @throws IndexException when $numberOfItemsToExtract is negative or if it would put the function out of bounds.
     * @throws IndexException when $startIndex is < 0 or >= $this->count()
     * @throws TypeException when $startIndex or $numberOfItemsToExtract are not integers.
     */
    function slice($startIndex, $numberOfItemsToExtract = NULL) {
        if (filter_var($startIndex, FILTER_VALIDATE_INT) === FALSE) {
            throw new TypeException;
        }
        if ($numberOfItemsToExtract !== NULL && filter_var($numberOfItemsToExtract, FILTER_VALIDATE_INT) === FALSE) {
            throw new TypeException;
        }

        if (!$this->offsetExists($startIndex)) {
            throw new IndexException;
        }

        $stopIndex = $numberOfItemsToExtract !== NULL
            ? $numberOfItemsToExtract + $startIndex
            : $this->count() - $startIndex;

        if ($numberOfItemsToExtract < 0 || !$this->offsetExists($stopIndex)) {
            throw new IndexException;
        }

        $slice = new Vector;

        $slice->array = array_slice($this->array, $startIndex, $numberOfItemsToExtract);

        return $slice;
    }

    /**
     * Filters elements of the vector using a callback function.
     *
     * @param callable $callable bool function($value, $key = NULL)
     *
     * @throws TypeException if $callable is not callable.
     * @return Vector
     */
    function filter($callable) {
        if (!is_callable($callable)) {
            throw new TypeException;
        }

        $vector = new Vector;

        foreach ($this->array as $i => $item) {
            if (call_user_func($callable, $item, $i)) {
                $vector->array[] = $item;
            }
        }

        return $vector;
    }

    /**
     * Creates a new vector with the result of calling $callable on each item.
     *
     * @param callable $callable mixed function($value, $key = NULL)
     *
     * @throws TypeException
     * @return Vector
     */
    function map($callable) {
        if (!is_callable($callable)) {
            throw new TypeException;
        }

        $vector = new Vector;

        $vector->array = array_map($callable, $this->array);

        return $vector;
    }

    /**
     * Applies $callable to each item in the vector.
     *
     * @param callable $callable function($value, $key = NULL)
     *
     * @throws TypeException
     * @return void
     */
    function apply($callable) {
        if (!is_callable($callable)) {
            throw new TypeException;
        }

        array_walk($this->array, $callable);
    }

    /**
     * @return array
     */
    function toArray() {
        return iterator_to_array($this->getIterator());
    }

    /**
     * @return VectorIterator
     */
    function getIterator() {
        return new VectorIterator($this);
    }

}
