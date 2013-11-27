<?php

namespace NodePub\Navigation;

use NodePub\Navigation\SitemapTree;
use NodePub\Navigation\SitemapLoader;

class Sitemap
{
    protected $tree,
              $activePath,
              $activeNode
              ;
  
    // function __construct(SitemapLoader $loader)
    // {
    //     $this->tree = $loader->load();
    // }
    
    function __construct($tree)
    {
        $this->tree = $tree;
    }
    
    /**
     * @return NodePub\Navigation\SitemapTree
     */
    public function getTree()
    {
        return $this->tree;
    }
    
    /**
     * Sets the path for determining the current page
     */
    public function setActivePath($path)
    {
        $this->activePath = $path;
        $nodes = $this->tree->getFlatList();
        foreach ($nodes as $node) {
            if ($node->getPath() == $path) {
                $node->setIsActive(true);
                $this->activeNode = $node;
                break;
            }
        }
    }
    
    public function getActiveNode()
    {
        return $this->activeNode;
    }

    /**
     * Returns an array of the active node and all its parent nodes,
     * for rendering breadcrumbs, etc.
     */
    public function getActiveNodes()
    {
        $nodes = array();
        $node = $this->activeNode;

        while ($node->getParent() instanceof SitemapTree) {
            $nodes[]= $node;
            $node = $node->getParent();
        }

        return $nodes;
    }
}
