<?php

namespace Ardent;

class LinkedQueueTest extends \PHPUnit_Framework_TestCase {

    /**
     * @covers \Ardent\LinkedQueue::__construct
     * @covers \Ardent\LinkedQueue::count
     * @covers \Ardent\LinkedQueue::getLinkedList
     * @covers \Ardent\LinkedQueue::pushBack
     */
    function testPushBack() {
        $queue = new LinkedQueue();

        $queue->pushBack(0);
        $this->assertCount(1, $queue);
        $list = $queue->getLinkedList();
        $this->assertEquals(0, $list->peekBack());

        $queue->pushBack(1);
        $this->assertCount(2, $queue);
        $list = $queue->getLinkedList();
        $this->assertEquals(0, $list->peekFront());
        $this->assertEquals(1, $list->peekBack());
    }

    /**
     * @depends testPushBack
     * @covers \Ardent\LinkedQueue::contains
     */
    function testContains() {
        $queue = new LinkedQueue();

        $this->assertFalse($queue->contains(0));

        $queue->pushBack(0);
        $this->assertTrue($queue->contains(0));

        $queue->pushBack(1);
        $this->assertTrue($queue->contains(0));
        $this->assertTrue($queue->contains(1));

        $this->assertFalse($queue->contains(-1));
    }

    /**
     * @depends testPushBack
     * @covers \Ardent\LinkedQueue::isEmpty
     */
    function testIsEmpty() {
        $queue = new LinkedQueue();
        $this->assertTrue($queue->isEmpty());

        $queue->pushBack(0);
        $this->assertFalse($queue->isEmpty());
    }

    /**
     * @covers \Ardent\LinkedQueue::peekFront
     * @expectedException \Ardent\EmptyException
     */
    function testPeekFrontEmpty() {
        $queue = new LinkedQueue();
        $queue->peekFront();
    }

    /**
     * @depends testPushBack
     * @covers \Ardent\LinkedQueue::peekFront
     */
    function testPeekFront() {
        $queue = new LinkedQueue();
        $queue->pushBack(0);

        $this->assertEquals(0, $queue->peekFront());
        $this->assertCount(1, $queue);

        $queue->pushBack(1);

        $this->assertEquals(0, $queue->peekFront());
        $this->assertCount(2, $queue);

        $list = $queue->getLinkedList();
        $this->assertEquals(0, $list->peekFront());
        $this->assertEquals(1, $list->peekBack());
    }

    /**
     * @covers \Ardent\LinkedQueue::popFront
     * @expectedException \Ardent\EmptyException
     */
    function testPopFrontEmpty() {
        $queue = new LinkedQueue();
        $queue->popFront();
    }

    /**
     * @depends testPushBack
     * @covers \Ardent\LinkedQueue::popFront
     */
    function testPushFront() {
        $queue = new LinkedQueue();
        $queue->pushBack(0);

        $this->assertEquals(0, $queue->popFront());
        $this->assertCount(0, $queue);

        $queue->pushBack(0);
        $queue->pushBack(1);

        $this->assertEquals(0, $queue->popFront());
        $this->assertCount(1, $queue);

        $list = $queue->getLinkedList();
        $this->assertEquals(1, $list->peekFront());
    }

    /**
     * @covers \Ardent\LinkedQueue::getIterator
     */
    function testGetIteratorEmpty() {
        $queue = new LinkedQueue();
        $iterator = $queue->getIterator();
        $this->assertInstanceOf('Ardent\\QueueIterator', $iterator);
    }

    /**
     * @covers \Ardent\LinkedQueue::getIterator
     * @covers \Ardent\LinkedQueueIterator::__construct
     * @covers \Ardent\LinkedQueueIterator::rewind
     * @covers \Ardent\LinkedQueueIterator::valid
     * @covers \Ardent\LinkedQueueIterator::key
     * @covers \Ardent\LinkedQueueIterator::current
     * @covers \Ardent\LinkedQueueIterator::next
     * @covers \Ardent\LinkedQueueIterator::count
     */
    function testIteratorForeach() {
        $queue = new LinkedQueue();
        $queue->pushBack(1);
        $queue->pushBack(2);
        $queue->pushBack(3);
        $queue->pushBack(4);

        $iterator = $queue->getIterator();
        $this->assertInstanceOf('Ardent\\LinkedQueueIterator', $iterator);

        $this->assertCount(4, $iterator);

        $expectedKey = 0;
        $expectedValue = 1;
        $iterator->rewind();

        for ($i = 0; $i < 4; $i++) {
            $this->assertTrue($iterator->valid());
            $this->assertEquals($expectedKey++, $iterator->key());
            $this->assertEquals($expectedValue++, $iterator->current());
            $iterator->next();
        }

        $this->assertFalse($iterator->valid());

        $iterator->next();
        $this->assertNull($iterator->key());
        $this->assertNull($iterator->current());

    }
}
