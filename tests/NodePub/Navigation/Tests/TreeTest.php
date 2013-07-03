<?php

namespace NodePub\Navigation\Tests;

use NodePub\Navigation\Tree;

class TreeTest extends \PHPUnit_Framework_TestCase
{
    protected $tree;

    public function setup()
    {
        $this->tree = new Tree('root');
        $this->child1 = new Tree('child1');
        $this->child2 = new Tree('child2');
        $this->child3 = new Tree('child3');
        $this->grandchild1 = new Tree('grandchild1');
        $this->grandchild2 = new Tree('grandchild2');
        $this->grandchild3 = new Tree('grandchild3');

        $this->tree->addNode($this->child1);
        $this->tree->addNode($this->child2);
        $this->tree->addNode($this->child3);

        $this->child2->addNode($this->grandchild1);
        $this->child2->addNode($this->grandchild2);
        $this->child2->addNode($this->grandchild3);
    }

    public function testGetAttribute()
    {
        $this->tree->setAttribute('foo', 'bar');
        $this->assertEquals('bar', $this->tree->getAttribute('foo'));
    }

    public function testGetAttributeDefault()
    {
        $this->assertEquals('bar', $this->tree->getAttribute('foo', 'bar'));
    }

    public function testGetAttributeNull()
    {
        $this->assertNull($this->tree->getAttribute('foo'));
    }

    public function testPrevSibling()
    {
        $this->assertSame($this->child1, $this->child2->prevSibling());
    }

    public function testPrevSiblingNull()
    {
        $this->assertNull($this->child1->prevSibling());
    }

    public function testNextSibling()
    {
        $this->assertSame($this->child2, $this->child1->nextSibling());
    }

    public function testNextSiblingNull()
    {
        $this->assertNull($this->child3->nextSibling());
    }

    public function testHasChildrenTrue()
    {
        $this->assertTrue($this->child2->hasChildren());
    }

    public function testHasChildrenFalse()
    {
        $this->assertFalse($this->child3->hasChildren());
    }

    public function testFirstChild()
    {
        $this->assertSame($this->child1, $this->tree->firstChild());
    }

    public function testFirstChildNull()
    {
        $this->assertNull($this->child1->firstChild());
    }

    public function testLastChild()
    {
        $this->assertSame($this->child3, $this->tree->lastChild());
    }

    public function testLastChildNull()
    {
        $this->assertNull($this->child1->lastChild());
    }

    public function testIsFirstChildTrue()
    {
        $this->assertTrue($this->child1->isFirstChild());
    }

    public function testIsFirstChildFalse()
    {
        $this->assertFalse($this->child3->isFirstChild());
    }

    public function testIsLastChildTrue()
    {
        $this->assertTrue($this->child3->isLastChild());
    }

    public function testIsLastChildFalse()
    {
        $this->assertFalse($this->child1->isLastChild());
    }

    public function testNodeCount()
    {
        $this->assertEquals(3, $this->tree->nodeCount());
    }

    public function testNodeCountSearch()
    {
        $this->assertEquals(6, $this->tree->nodeCount(true));
    }

    public function testDepthRoot()
    {
        $this->assertEquals(0, $this->tree->depth());
    }

    public function testDepthGrandchild()
    {
        $this->assertEquals(2, $this->grandchild1->depth());
    }

    public function testIsChildOfTrue()
    {
        $this->assertTrue($this->grandchild1->isChildOf($this->child2));
    }

    public function testIsChildOfFalse()
    {
        $this->assertFalse($this->grandchild1->isChildOf($this->tree));
    }

    public function testAddNode()
    {
        $this->tree->addNode(new Tree('foo'));
        $this->assertEquals(4, $this->tree->nodeCount());
    }

    public function testAddNodeAsText()
    {
        $this->tree->addNode('foo');
        $this->tree->addNode('bar');
        $this->assertEquals(5, $this->tree->nodeCount());
    }

    // moveTo

    // copyTo

    // removeNodeAt

    // remove

    public function testIndexOf()
    {
        $this->assertEquals(0, $this->tree->indexOf($this->child1));
        $this->assertEquals(1, $this->tree->indexOf($this->child2));
        $this->assertEquals(2, $this->tree->indexOf($this->child3));
    }

    public function testIndexOfNull()
    {
        $this->assertNull($this->tree->indexOf($this->grandchild1));
    }

    public function testNodeAt()
    {
        $this->assertSame($this->child1, $this->tree->nodeAt(0));
        $this->assertNull($this->tree->nodeAt(1000));
    }

    public function testGetFlatList()
    {
        $expected = array(
            $this->tree,
            $this->child1,
            $this->child2,
            $this->grandchild1,
            $this->grandchild2,
            $this->grandchild3,
            $this->child3
        );

        $result = $this->tree->getFlatList();

        $this->assertEquals($expected, $result);
    }

    // traverse

    public function testSearch()
    {
        $result = $this->tree->search('grandchild2');
        $expected = array($this->grandchild2);

        $this->assertEquals($expected, $result);
    }

    public function testSearchMultiple()
    {
        $foo = $this->tree->addNode('grandchild2');
        $expected = array($this->grandchild2, $foo);
        $result = $this->tree->search('grandchild2');

        $this->assertEquals($expected, $result);
    }

    public function testSearchEmpty()
    {
        $result = $this->tree->search('not here');
        $this->assertEquals(array(), $result);
    }

    /**
     * Tests proper implementation of IteratorAggregate
     */
    public function testGetIterator()
    {
        foreach ($this->tree as $key => $node) {
            $this->assertSame($node, $this->tree->offsetGet($key));
        }
    }

    public function testOffsetSet()
    {
        $node = new Tree('foo');
        $this->tree->offsetSet('node', $node);

        $this->assertSame($node, $this->tree->offsetGet('node'));
    }

    public function testOffsetSetArrayAccess()
    {
        $node = new Tree('foo');
        $this->tree['node'] = $node;

        $this->assertSame($node, $this->tree->offsetGet('node'));
    }

    public function testOffsetGet()
    {
        $this->assertSame($this->child1, $this->tree->offsetGet(0));
    }

    public function testOffsetGetArrayAccess()
    {
        $this->assertSame($this->child1, $this->tree[0]);
    }

    public function testOffsetUnset()
    {
        $this->tree->offsetUnset(0);
        $this->assertFalse($this->tree->offsetExists(0));
    }

    public function testOffsetUnsetArrayAccess()
    {
        unset($this->tree[0]);
        $this->assertFalse($this->tree->offsetExists(0));
    }

    public function testOffsetExistsTrue()
    {
        $this->assertTrue($this->tree->offsetExists(0));
    }

    public function testOffsetExistsArrayAccessTrue()
    {
        $this->assertTrue(isset($this->tree[0]));
    }

    public function testOffsetExistsFalse()
    {
        $this->assertFalse($this->tree->offsetExists('foo'));
    }

    public function testOffsetExistsArrayAccessFalse()
    {
        $this->assertFalse(isset($this->tree['foo']));
    }
}