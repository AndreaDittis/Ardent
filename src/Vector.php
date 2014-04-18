<?php

namespace Collections;

class Vector implements \ArrayAccess, \Countable, Enumerable {

    protected $array = [];


    /**
     * @param mixed,... $varargs
     * @throws TypeException
     */
    function __construct($varargs = NULL) {
        $this->array = func_get_args();
    }


    /**
     * @param \Traversable $traversable
     * @return void
     */
    function appendAll(\Traversable $traversable) {
        foreach ($traversable as $item) {
            $this->array[] = $item;
        }
    }


    /**
     * @return void
     */
    function clear() {
        $this->array = [];
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
    function offsetExists($offset) {
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
    function offsetGet($offset) {
        $index = $this->existsGuard(intGuard($offset));
        return $this->array[$index];
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
    function offsetSet($offset, $value) {
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
    function offsetUnset($offset) {
        $this->remove($offset);
    }


    /**
     * @link http://php.net/manual/en/countable.count.php
     * @return int
     */
    function count() {
        return count($this->array);
    }


    /**
     * @param $item
     *
     * @return void
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
        return $this->offsetGet($index);
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
        $ndx = $this->existsGuard(intGuard($index));
        $this->array[$ndx] = $item;
    }


    /**
     * @param int $index
     *
     * @throws TypeException when $index is not an integer.
     * @return void
     */
    function remove($index) {
        $ndx = intGuard($index);
        if ($this->offsetExists($ndx)) {
            array_splice($this->array, $ndx, 1);
        }
    }


    /**
     * @param mixed $object
     *
     * @throws TypeException if $item is the incorrect type for the Vector
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
     * Applies $callable to each item in the vector.
     *
     * @param callable $callable function($value, $key = NULL)
     * @return void
     */
    function apply(callable $callable) {
        foreach ($this->array as $i => $value) {
            $this->array[$i] = call_user_func($callable, $value, $i);
        }
    }


    /**
     * @param callable $map
     * @return Vector
     */
    function map(callable $map): Vector {
        $vector = new self();
        $vector->appendAll($this->getIterator()->map($map));
        return $vector;
    }


    /**
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return VectorIterator
     */
    function getIterator(): VectorIterator {
        return new VectorIterator($this);
    }


    /**
     * @param int $n
     * @return Vector
     */
    function limit($n): Vector {
        $v = new Vector();
        $v->array = array_slice($this->array, 0, $n);
        return $v;
    }


    /**
     * @param int $n
     * @return Vector
     */
    function skip($n): Vector {
        $v = new Vector();
        $v->array = array_slice($this->array, $n);
        return $v;
    }


    /**
     * @param int $start
     * @param int $count
     * @return Vector
     */
    function slice($start, $count): Vector {
        $v = new Vector();
        $v->array = array_slice($this->array, $start, $count);
        return $v;
    }


    function toArray(): array {
       return $this->array;
    }


    /**
     * @param $initialValue
     * @param callable $combine
     * @return mixed
     */
    function reduce($initialValue, callable $combine) {
        return $this->getIterator()->reduce($initialValue, $combine);
    }


    function filter(callable $filter): Vector {
        $vector = new self();
        $vector->appendAll($this->getIterator()->filter($filter));
        return $vector;
    }


    function keys(): Vector {
        $vector = new Vector();
        $vector->appendAll($this->getIterator()->keys());
        return $vector;
    }


    function values(): Vector {
        return $this;
    }


    /**
     * @param int $i
     * @return mixed
     * @throws IndexException
     */
    private function existsGuard($i) {
        if (!$this->offsetExists($i)) {
            throw new IndexException;
        }
        return $i;
    }


}
