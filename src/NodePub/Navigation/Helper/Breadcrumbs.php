<?php

namespace NodePub\Navigation\Helper;

use NodePub\Navigation\Sitemap;

class Breadcrumbs
{
    protected $sitemap;
  
    function __construct(Sitemap $sitemap, array $options = array())
    {
        $this->sitemap = $sitemap;
    }

    public function render($options = array())
    {
        foreach ($this->sitemap->getActiveNodes() as $node) {
            
        }
    }
}
