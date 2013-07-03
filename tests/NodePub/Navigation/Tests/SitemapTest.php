<?php

namespace NodePub\Navigation\Tests;

use NodePub\Navigation\SitemapTree;
use NodePub\Navigation\Sitemap;

class SitemapTest extends \PHPUnit_Framework_TestCase
{
    protected $sitemap;

    public function setup()
    {
        $this->sitemapTree = new SitemapTree('root');
        $this->sitemapTree->setIsRoot(true);
        $this->sitemapTree->setHref('/');

        $this->child1 = $this->sitemapTree->addNode('child1');
        $this->child1->setHref('/child1');

        $this->child2 = $this->sitemapTree->addNode('child2');
        $this->child2->setHref('/child2');

        $this->grandchild1 = $this->child2->addNode('grandchild1');
        $this->grandchild1->setHref('/child2/grandchild1');

        $this->grandchild2 = $this->child2->addNode('grandchild2');
        $this->grandchild2->setHref('/child2/grandchild2');

        $this->grandchild3 = $this->child2->addNode('grandchild3');
        $this->grandchild3->setHref('/child2/grandchild3');

        $this->child3 = $this->sitemapTree->addNode('child3');
        $this->child3->setHref('/child3');

        $loader = $this->getMockBuilder('NodePub\Navigation\SitemapLoader')
            ->disableOriginalConstructor()
            ->setMethods(array('load'))
            ->getMock();

        $loader->expects($this->once())
            ->method('load')
            ->will($this->returnValue($this->sitemapTree));

        $this->sitemap = new Sitemap($loader);
    }

    public function testGetActiveNode()
    {
        $this->sitemap->setActivePath('/child2/grandchild3');

        $this->assertSame($this->grandchild3, $this->sitemap->getActiveNode());
    }

    public function testGetActiveNodeNull()
    {
        $this->assertNull($this->sitemap->getActiveNode());
    }

    public function testGetActiveNodes()
    {
        $this->sitemap->setActivePath('/child2/grandchild3');

        $expected = array($this->grandchild3, $this->child2);

        $this->assertEquals($expected, $this->sitemap->getActiveNodes());
    }
}
