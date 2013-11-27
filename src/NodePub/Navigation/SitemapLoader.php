<?php

namespace NodePub\Navigation;

use NodePub\Navigation\SitemapTree;
use NodePub\Navigation\SitemapCache;
use Symfony\Component\Yaml\Yaml;

class SitemapLoader
{
    protected $configFile,
              $cacheFile,
              $slugHelper
              ;
  
    function __construct($configFile, $cacheFile)
    {
        $this->configFile = $configFile;
        $this->cacheFile = $cacheFile;
    }
    
    /**
     * Loads the sitemap tree from config or cache
     * If loading from config, caches the result
     *
     * @return NodePub\Navigation\SitemapTree
     */
    public function load()
    {
        $sitemapTree = new SitemapTree('root');
        $sitemapTree->setIsRoot(true);

        $cache = new SitemapCache($this->cacheFile);
        
        if ($serializedArray = $cache->load()) {
            $sitemapTree->unserialize($serializedArray);
        } elseif (is_file($this->configFile)) {
            $sitemapTree = $this->expandChildNodes($parent, Yaml::load($this->configFile));
            $cache->cacheSerializedArray($sitemapTree->serialize());
        }
        
        return $sitemapTree;
    }
  
    /**
     * Converts the shorthand sitemap config array into a tree structure.
     *
     * @param array          $children    The items to add as child nodes to the given $parent
     * @param TreeSitemap    $parent      The parent tree object
     * @param string         $path        The root-relative base url
     *
     * @return boolean true if ok, otherwise false
     */
    protected function expandChildNodes(SitemapTree $parent, array $children, $path='/')
    {
        foreach ($children as $key => $value) {
            if (is_numeric($key) && is_string($value)) {
                $nodeValue = $value;
            } elseif (is_string($key) && is_array($value)) {
                $nodeValue = $key;
            } else {
                $nodeValue = $key;
                if ($value != '~') {
                    # assume a hardcoded url: 'Google' => 'google.com'
                    $url = $value;
                }
            }
            
            $node = $parent->addNode($nodeValue);
            $slug = $this->slugify($nodeValue);
            $node->setSlug($slug);
            
            $href = isset($url) ? $url : $path.$slug;
            $node->setHref($href);
            
            # recurse
            if (is_array($value)) {
                $this->expandChildNodes($node, $value, $path.$slug.'/');
            }
        }
        
        return $parent;
    }

    public function setSlugHelper($slugHelper)
    {
        if (method_exists($slugHelper, 'slugify')) {
            $this->slugHelper = $slugHelper;
        }
    }

    protected function slugify($text)
    {
        if (isset($this->slugHelper)) {
            $text = $this->slugHelper->slugify($text);
        } else {
            $text = strtolower(str_replace(' ', '-', $text));
        }

        return $text;
    }
}
