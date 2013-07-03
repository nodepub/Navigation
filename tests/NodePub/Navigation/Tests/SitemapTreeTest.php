<?php

namespace NodePub\Navigation\Tests;

use NodePub\Navigation\SitemapTree;

class SitemapTreeTest extends \PHPUnit_Framework_TestCase
{
    protected $sitemap;

    public function setup()
    {
        $this->sitemap = new SitemapTree('root');
        $this->sitemap->setIsRoot(true);

        $this->child1 = new SitemapTree('child1');
        $this->child2 = new SitemapTree('child2');
        $this->child3 = new SitemapTree('child3');
        $this->grandchild1 = new SitemapTree('grandchild1');
        $this->grandchild2 = new SitemapTree('grandchild2');
        $this->grandchild3 = new SitemapTree('grandchild3');

        $this->sitemap->addNode($this->child1);
        $this->sitemap->addNode($this->child2);
        $this->sitemap->addNode($this->child3);

        $this->child2->addNode($this->grandchild1);
        $this->child2->addNode($this->grandchild2);
        $this->child2->addNode($this->grandchild3);
    }

    public function testSetIsActive()
    {
        $this->grandchild3->setIsActive(true);
        $this->assertTrue($this->grandchild3->isActive());
    }

    /**
     * Tests that siblings of the active node
     * are not set as active
     */
    public function testSetIsActiveFalse()
    {
        $this->grandchild3->setIsActive(true);
        $this->assertFalse($this->grandchild1->isActive());
    }

    /**
     * Tests that the parent of the active node
     * is also set as active
     */
    public function testSetIsActiveParent()
    {
        $this->grandchild3->setIsActive(true);
        $this->assertTrue($this->child2->isActive());
    }

    /**
     * Tests that a node 1 level up is not set as active
     * if it is not the parent of the active node
     */
    public function testSetIsActiveParentFalse()
    {
        $this->grandchild3->setIsActive(true);
        $this->assertFalse($this->child1->isActive());
    }

    /**
     * Tests that the root node is also set as active once
     * a child node is set as active, showing that it's 
     */
    public function testSetIsActiveRoot()
    {
        $this->grandchild3->setIsActive(true);
        $this->assertTrue($this->sitemap->isActive());
    }

    public function testIsRootTrue()
    {
        $this->assertTrue($this->sitemap->isRoot());
    }

    public function testIsRootFalse()
    {
        $this->assertFalse($this->child1->isRoot());
    }

    /**
     * Tests that a child node can't be set as root
     */
    public function testIsRootChild()
    {
        $this->child1->setIsRoot(true);
        $this->assertFalse($this->child1->isRoot());
    }

    public function testIsTopLevel()
    {
        $this->assertTrue($this->child1->isTopLevel());
        $this->assertTrue($this->child2->isTopLevel());
    }

    public function testIsTopLevelFalse()
    {
        $this->assertFalse($this->sitemap->isTopLevel());
        $this->assertFalse($this->grandchild1->isTopLevel());
        $this->assertFalse($this->grandchild2->isTopLevel());
    }
}
