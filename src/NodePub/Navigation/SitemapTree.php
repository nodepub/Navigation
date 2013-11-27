<?php

namespace NodePub\Navigation;

use NodePub\Navigation\Tree;

/**
 * Extends Tree with specific attributes used for storing a sitemap object.
 */
class SitemapTree extends Tree implements \Serializable
{
    const ATTR_IS_ACTIVE = 'isActive';
    const ATTR_IS_ACTIVE_PARENT = 'isActiveParent';
    const ATTR_PATH = 'path';
    const ATTR_SLUG = 'slug';
    const ATTR_IS_ROOT = 'isRoot';
  
    # ============================================================================ #
    # Accessors                                                                    #
    # ============================================================================ #
    
    public function setPath($path)
    {
        $this->setAttribute(self::ATTR_PATH, $path);
        
        return $this;
    }
    
    public function getPath()
    {
        return $this->getAttribute(self::ATTR_PATH, '');
    }
    
    public function isActive()
    {
        return $this->getAttribute(self::ATTR_IS_ACTIVE) === true;
    }
    
    /**
     * Sets this node's 'isActive' attribute,
     * and calls setIsActiveParent() on this node's parent
     */
    public function setIsActive($value)
    {
        $this->setAttribute(self::ATTR_IS_ACTIVE, (bool) $value);
        
        if ($parent = $this->getParent()) {
            $parent->setIsActive($value);
        }
        
        return $this;
    }
    
    /**
     * Alias for parent::setValue()
     */
    public function setName($name)
    {
        $this->value = $name;
        
        return $this;
    }
    
    /**
     * Alias for parent::getValue()
     */
    public function getName()
    {
        return $this->value;
    }
    
    public function setSlug($slug)
    {
        $this->setAttribute(self::ATTR_SLUG, $slug);
        
        return $this;
    }
    
    public function getSlug()
    {
        return $this->getAttribute(self::ATTR_SLUG);
    }
    
    public function setIsRoot($value)
    {
        $this->setAttribute(self::ATTR_IS_ROOT, (bool) $value);
        
        return $this;
    }
    
    public function isRoot()
    {
        return is_null($this->getParent()) && $this->getAttribute(self::ATTR_IS_ROOT) === true;
    }
    
    public function isTopLevel()
    {
        return $this->getParent() && $this->getParent()->isRoot();
    }
    
    // Not sure we need a dynamic way to generate paths by reading up the tree,
    // when we're already setting the path on creation
    // public function getDynamicPath()
    // {
    //     $path = $this->slug;
        
    //     if ($parent = $this->getParent()) {
    //         $path = $parent->getPath().'/'.$path;
    //     }

    //     return '/'.$path;
    // }
    
    /**
     * Converts into an associative array
     */
    public function serialize()
    {
        $childNodes = array();
        
        # recurse
        foreach ($node->nodes as $child) {
            $childNodes[]= $child->serialize();
        }
        
        # hide the root node in order to have multiple top level items
        if (!$node->isRoot()) {
            $nodeArray = array(
                'name' => $this->getName(),
                'path' => $this->getPath()
            );
            
            if ($this->hasChildren()) {
                $nodeArray['nodes']= $childNodes;
            }
            
            return $nodeArray;
        } else {
            return $childNodes;
        }
    }
    
    /**
     * Converts a serialized array back into a SitemapTree object
     */
    public function unserialize($importArray=array())
    {
        foreach ($importArray as $nodeArray) {
            if (isset($nodeArray['name']) && isset($nodeArray['href'])) {
                $node = new SitemapTree($nodeArray['name']);
                $node->setHref($nodeArray['href']);
                if (isset($nodeArray['nodes']) && is_array($nodeArray['nodes'])) {
                    $node->unserialize($nodeArray['nodes']);
                }
        
                $this->addNode($node);
            }
        }
    }
}