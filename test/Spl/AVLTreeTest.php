<?php
namespace Spl;

class AVLTreeHelper extends AVLTree {

    public function getRoot() {
        return parent::getRoot();
    }

}

/**
 * Generated by PHPUnit_SkeletonGenerator on 2012-08-02 at 12:46:38.
 */
class AVLTreeTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var AVLTreeHelper
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->object = new AVLTreeHelper();
    }

    /**
     * @covers Spl\AVLTree::add
     * @covers Spl\AVLTree::balance
     */
    public function testRightLeft() {
        $this->object->add(3);
        $this->object->add(5);
        $this->object->add(4);

        $root = new BinaryNode(4);
        $root->setLeft(new BinaryNode(3));
        $root->setRight(new BinaryNode(5));

        $this->reCalculateHeights($root);
        $this->assertEquals($root, $this->object->getRoot());

    }

    public function testLeftRight() {
        $this->object->add(5);
        $this->object->add(3);
        $this->object->add(4);

        $root = new BinaryNode(4);
        $root->setLeft(new BinaryNode(3));
        $root->setRight(new BinaryNode(5));

        $this->reCalculateHeights($root);
        $this->assertEquals($root, $this->object->getRoot());
    }

    public function testRemoveRootBasic() {
        $this->object->add(5);
        $this->object->remove(5);

        $root = $this->object->getRoot();
        $this->assertNull($root);
    }

    /**
     * @covers Spl\AVLTree::remove
     * @depends testRightLeft
     * @depends testLeftRight
     */
    public function testRemoveLeaf() {
        $this->object->add(4);
        $this->object->add(3);
        $this->object->add(5);

        $this->object->remove(3);

        $root = new BinaryNode(4);
        $root->setRight(new BinaryNode(5));
        $this->reCalculateHeights($root);

        $this->assertEquals($root, $this->object->getRoot());
    }

    /**
     * @covers Spl\AVLTree::remove
     * @depends testRightLeft
     * @depends testLeftRight
     */
    public function testRemoveWithLeftChild() {
        $this->object->add(4);
        $this->object->add(3);
        $this->object->add(5);
        $this->object->add(1);

        $this->object->remove(3);

        $root = new BinaryNode(4);
        $root->setLeft(new BinaryNode(1));
        $root->setRight(new BinaryNode(5));
        $this->reCalculateHeights($root);

        $this->assertEquals($root, $this->object->getRoot());
    }

    /**
     * @covers Spl\AVLTree::remove
     * @depends testRightLeft
     * @depends testLeftRight
     */
    public function testRemoveWithRightChild() {
        $this->object->add(4);
        $this->object->add(3);
        $this->object->add(5);
        $this->object->add(7);

        $this->object->remove(5);

        $root = new BinaryNode(4);
        $root->setLeft(new BinaryNode(3));
        $root->setRight(new BinaryNode(7));
        $this->reCalculateHeights($root);

        $this->assertEquals($root, $this->object->getRoot());
    }

    public function testRemoveWithBothChildren() {
        $this->object->add(4);
        $this->object->add(3);
        $this->object->add(5);

        $this->object->remove(4);

        $root = new BinaryNode(3);
        $root->setRight(new BinaryNode(5));
        $this->reCalculateHeights($root);

        $this->assertEquals($root, $this->object->getRoot());
    }

    /**
     * @covers Spl\AVLTree::add
     * @covers Spl\AVLTree::remove
     * @covers Spl\AVLTree::balance
     */
    public function testGauntlet() {

        // add a bunch of items!
        $this->object->add(8);
        $this->object->add(10);
        $this->object->add(-5);

        $this->object->add(2);

        //triggers right-right case
        $this->object->add(4);

        $this->object->add(-1);


        $this->object->add(3);
        $this->object->add(5);

        //triggers left-right case
        $this->object->add(6);


        //         2
        //      /     \
        //   -5        5
        //     \     /   \
        //     -1   4     8
        //         /     / \
        //        3     6  10

        $root = new BinaryNode(2);
        $root->setLeft(new BinaryNode(-5));
        $root->setRight(new BinaryNode(5));

        $root->getLeft()->setRight(new BinaryNode(-1));
        $root->getRight()->setLeft(new BinaryNode(4));
        $root->getRight()->setRight(new BinaryNode(8));

        $root->getRight()->getLeft()->setLeft(new BinaryNode(3));
        $root->getRight()->getRight()->setLeft(new BinaryNode(6));
        $root->getRight()->getRight()->setRight(new BinaryNode(10));

        $this->reCalculateHeights($root);
        $this->assertEquals($root, $this->object->getRoot());


        $this->object->add(-2);
        $this->object->add(-6);

        //triggers left-left
        $this->object->add(-7);

        //            2
        //         /     \
        //      -2        5
        //      / \     /   \
        //    -6  -1   4     8
        //    / \     /     / \
        //  -7  -5   3     6  10


        $root = new BinaryNode(2);
        $root->setLeft(new BinaryNode(-2));
        $root->setRight(new BinaryNode(5));

        $root->getLeft()->setLeft(new BinaryNode(-6));
        $root->getLeft()->setRight(new BinaryNode(-1));
        $root->getRight()->setLeft(new BinaryNode(4));
        $root->getRight()->setRight(new BinaryNode(8));

        $root->getLeft()->getLeft()->setLeft(new BinaryNode(-7));
        $root->getLeft()->getLeft()->setRight(new BinaryNode(-5));
        $root->getRight()->getLeft()->setLeft(new BinaryNode(3));
        $root->getRight()->getRight()->setLeft(new BinaryNode(6));
        $root->getRight()->getRight()->setRight(new BinaryNode(10));

        $this->reCalculateHeights($root);
        $this->assertEquals($root, $this->object->getRoot());


        // begin removing items
        $this->object->remove(6);
        $this->object->remove(10);
        $this->object->remove(8); //triggers rotation

        //            2
        //         /     \
        //      -2        4
        //      / \     /   \
        //    -6  -1   3     5
        //    / \
        //  -7  -5

        $root = new BinaryNode(2);
        $root->setLeft(new BinaryNode(-2));
        $root->setRight(new BinaryNode(4));

        $root->getLeft()->setLeft(new BinaryNode(-6));
        $root->getLeft()->setRight(new BinaryNode(-1));
        $root->getRight()->setLeft(new BinaryNode(3));
        $root->getRight()->setRight(new BinaryNode(5));
        $root->getLeft()->getLeft()->setLeft(new BinaryNode(-7));
        $root->getLeft()->getLeft()->setRight(new BinaryNode(-5));
        $this->reCalculateHeights($root);

        $actualRoot = $this->object->getRoot();

        $this->assertEquals($root, $actualRoot);

        //remove root
        $this->object->remove(2);

        //            -1
        //         /     \
        //      -6        4
        //      / \     /   \
        //    -7  -2   3    5
        //        /
        //      -5

        $root = new BinaryNode(-1);
        $root->setLeft(new BinaryNode(-6));
        $root->setRight(new BinaryNode(4));

        $root->getLeft()->setLeft(new BinaryNode(-7));
        $root->getLeft()->setRight(new BinaryNode(-2));
        $root->getRight()->setLeft(new BinaryNode(3));
        $root->getRight()->setRight(new BinaryNode(5));
        $root->getLeft()->getRight()->setLeft(new BinaryNode(-5));
        $this->reCalculateHeights($root);

        $actualRoot = $this->object->getRoot();
        $this->assertEquals($root, $actualRoot);
    }

    private function reCalculateHeights(BinaryNode $root = NULL) {
        if ($root === NULL) {
            return;
        }
        $this->reCalculateHeights($root->getLeft());
        $this->reCalculateHeights($root->getRight());
        $root->recalculateHeight();

    }

    /**
     * @covers Spl\AVLTree::clear
     */
    public function testClear() {
        $this->object->add(5);

        $this->object->clear();

        $this->assertNull($this->object->getRoot());
        $this->assertEmpty($this->object->count());
    }

    /**
     * @covers Spl\AVLTree::contains
     */
    public function testContains() {
        $this->assertFalse($this->object->contains(1));

        $this->object->add(1);
        $this->assertTrue($this->object->contains(1));
    }

    /**
     * @covers Spl\AVLTree::isEmpty
     */
    public function testIsEmpty() {
        $this->assertTrue($this->object->isEmpty());

        $this->object->add(1);
        $this->assertFalse($this->object->isEmpty());
    }

}
