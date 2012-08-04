<?php

namespace Spl;

use Iterator,
    Traversable;

class HashSet implements Iterator, Set {

    /**
     * @var array
     */
    private $objects = array();

    /**
     * @var callable
     */
    protected $hashFunction = NULL;

    /**
     * @param callable $hashFunction
     *
     * @return \Spl\HashSet
     */
    public function __construct($hashFunction = NULL) {
        if ($hashFunction === NULL) {
            $hashFunction = array($this, 'hash');
        }
        $this->hashFunction = $hashFunction;
    }

    /**
     * @param $item
     *
     * @return string
     */
    private function hash($item) {
        if (is_object($item)) {
            return spl_object_hash($item);
        } elseif (is_scalar($item)) {
            return "s_$item";
        } elseif (is_resource($item)) {
            return "r_$item";
        } elseif (is_array($item)) {
            return 'a_' . md5(serialize($item));
        }

        return '0';
    }

    /**
     * @return void
     */
    public function clear() {
        $this->objects = array();
    }

    /**
     * @param $object
     *
     * @return bool
     * @throws InvalidTypeException when $object is not the correct type.
     */
    public function contains($object) {
        return array_key_exists(call_user_func($this->hashFunction, $object), $this->objects);
    }

    /**
     * @return bool
     */
    public function isEmpty() {
        return $this->count() === 0;
    }

    /**
     * @link http://php.net/manual/en/countable.count.php
     * @return int
     */
    public function count() {
        return count($this->objects);
    }

    /**
     * @param $item
     *
     * @return void
     * @throws InvalidTypeException when $item is not the correct type.
     */
    public function add($item) {
        $this->objects[call_user_func($this->hashFunction, $item)] = $item;
    }

    /**
     * @param Traversable $items
     *
     * @return void
     * @throws InvalidTypeException when the Traversable does not include an item of the correct type.
     */
    public function addAll(Traversable $items) {
        foreach ($items as $item) {
            $this->add($item);
        }
    }

    /**
     * @param $item
     *
     * @return void
     * @throws InvalidTypeException when $item is not the correct type.
     */
    public function remove($item) {
        unset($this->objects[call_user_func($this->hashFunction, $item)]);
    }

    /**
     * @param Traversable $items
     *
     * @return mixed
     * @throws InvalidTypeException when the Traversable does not include an item of the correct type.
     */
    public function removeAll(Traversable $items) {
        foreach ($items as $item) {
            $this->remove($item);
        }
    }

    /**
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed
     */
    public function current() {
        return current($this->objects);
    }

    /**
     * @link http://php.net/manual/en/iterator.next.php
     * @return void
     */
    public function next() {
        next($this->objects);
    }

    /**
     * @link http://php.net/manual/en/iterator.key.php
     * @return string
     */
    public function key() {
        return key($this->objects);
    }

    /**
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean
     */
    public function valid() {
        return key($this->objects) !== NULL;
    }

    /**
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void
     */
    public function rewind() {
        reset($this->objects);
    }

}
