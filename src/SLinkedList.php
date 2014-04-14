<?php

namespace Collections;


class SLinkedList implements \ArrayAccess, \Countable, Enumerator {

    use IteratorCollection;

    private $head;
    private $tail;
    private $size = 0;
    private $current;
    private $offset = -1;

    function __construct() {
        $this->head = $head = new STerminalNode();
        $this->tail = $tail = new STerminalNode();

        $head->setNext($tail);
        $tail->setPrev($head);

        $this->current = $this->head;
    }

    function isEmpty() {
        return $this->size === 0;
    }


    function push($value) {
        $this->insertBetween($this->tail->prev(), $this->tail, $value);
    }


    function unshift($value) {
        $this->insertBetween($this->head, $this->head->next(), $value);
    }


    function pop() {
        if ($this->isEmpty()) {
            throw new EmptyException;
        }

        /**
         * @var SDataNode $n
         */
        $n = $this->tail->prev();
        $this->current = $n->prev();
        $this->offset = $this->size -1;
        $this->removeNode($n);
        return $n->value();
    }


    function shift() {
        if ($this->isEmpty()) {
            throw new EmptyException;
        }

        $n = $this->seekTo(0);
        $this->removeNode($n);
        return $n->value();
    }


    function first() {
        if ($this->isEmpty()) {
            throw new EmptyException;
        }

        return $this->seek(0);
    }


    function last() {
        if ($this->isEmpty()) {
            throw new EmptyException;
        }

        return $this->seek($this->size - 1);
    }


    /**
     * @link http://php.net/manual/en/countable.count.php
     * @return int
     */
    function count() {
        return $this->size;
    }


    /**
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param int $offset
     * @return boolean
     */
    function offsetExists($offset) {
        return $offset >= 0 && $offset < $this->count();
    }


    /**
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset
     * @return mixed
     * @throws IndexException
     */
    function offsetGet($offset) {
        if (!$this->offsetExists($offset)) {
            throw new IndexException;
        }
        $n = $this->seekTo($offset);
        return $n->value();
    }


    /**
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param int|null $offset
     * @param mixed $value
     * @return void
     * @throws IndexException
     */
    function offsetSet($offset, $value) {
        if ($offset === null) {
            $this->push($value);
            return;
        }
        if (!$this->offsetExists($offset)) {
            throw new IndexException;
        }
        $n = $this->seekTo($offset);
        $n->setValue($value);
    }


    /**
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset
     * @return void
     */
    function offsetUnset($offset) {
        if (!$this->offsetExists($offset)) {
            return;
        }
        $n = $this->seekTo($offset);
        $this->removeNode($n);
        $this->current = $n->prev();
        $this->offset--;
    }


    /**
     * @param int $position
     * @param mixed $value
     * @return void
     * @throws IndexException
     */
    function insertBefore($position, $value) {
        if (!$this->offsetExists($position)) {
            throw new IndexException;
        }
        $n = $this->seekTo($position);
        $this->insertBetween($n->prev(), $n, $value);
        $this->current = $this->current->next();
        $this->offset++;
    }


    /**
     * @param int $position
     * @param mixed $value
     * @return void
     * @throws IndexException
     */
    function insertAfter($position, $value) {
        if (!$this->offsetExists($position)) {
            throw new IndexException;
        }
        $n = $this->seekTo($position);
        $this->insertBetween($n, $n->next(), $value);
        $this->current = $this->current->prev();
    }


    /**
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed
     */
    function current() {
        /**
         * @var SDataNode $n
         */
        $n = $this->current;
        return $n->value();
    }


    /**
     * @link http://php.net/manual/en/iterator.next.php
     * @return void
     */
    function next() {
        $this->forward();
    }


    /**
     * @return void
     */
    function prev() {
        $this->backward();
    }


    /**
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed
     */
    function key() {
        return $this->offset;
    }


    /**
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean
     */
    function valid() {
        return $this->current instanceof SDataNode;
    }


    /**
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void
     */
    function rewind() {
        $this->current = $this->head;
        $this->offset = -1;
        $this->forward();
    }


    /**
     * @link http://php.net/manual/en/seekableiterator.seek.php
     * @param int $position
     * @return mixed
     * @throws IndexException
     */

    function seek($position) {
        if ($position < 0 || $position >= $this->size) {
            throw new IndexException;
        }

        return $this->seekTo($position)->value();
    }


    /**
     * Extract the elements after the first of a list, which must be non-empty.
     * @return SLinkedList
     * @throws EmptyException
     */
    function tail() {
        if ($this->isEmpty()) {
            throw new EmptyException;
        }
        return $this->copyFromContext($this->head->next()->next());
    }


    function __clone() {
        $list = $this->copyFromContext($this->head->next());
        $this->head = $list->head;
        $this->tail = $list->tail;
        $this->current = $this->head;
        $this->offset = -1;
        $this->size = $list->size;
    }


    private function copyFromContext(SNode $context) {
        $list = new self();
        for ($n = $context; $n !== $this->tail; $n = $n->next()) {
            /**
             * @var SDataNode $n
             */
            $list->push($n->value());
        }
        return $list;
    }


    private function removeNode(SNode $n) {
        $prev = $n->prev();
        $next = $n->next();

        $prev->setNext($next);
        $next->setPrev($prev);
        $this->size--;
    }


    private function insertBetween(SNode $a, SNode $b, $value) {
        $n = new SDataNode($value);
        $a->setNext($n);
        $b->setPrev($n);

        $n->setPrev($a);
        $n->setNext($b);

        $this->current = $n;
        $this->size++;
    }


    private function forward() {
        $this->current = $this->current->next();
        $this->offset++;
    }


    private function backward() {
        $this->current = $this->current->prev();
        $this->offset--;
    }


    /**
     * @param $offset
     * @return SDataNode
     */
    private function seekTo($offset) {
        if ($offset == $this->offset) {
            return $this->current;
        }

        if ($offset == 0) {
            $this->offset = 0;
            return $this->current = $this->head->next();
        }

        if ($offset == $this->size - 1) {
            $this->offset = $this->size - 1;
            $this->current = $this->tail->prev();
            return $this->current;
        }

        $diff = $this->offset - $offset;
        if ($diff < 0) {
            for ($i = 0; $i > $diff; $i--) {
                $this->forward();
            }
        } else {
            for ($i = 0; $i < $diff; $i++) {
                $this->backward();
            }
        }

        return $this->current;
    }


}