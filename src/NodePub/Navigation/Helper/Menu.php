<?php

namespace NodePub\Navigation\Helper;

use NodePub\Navigation\Sitemap;

class Menu
{
    protected $sitemap;
  
    function __construct(Sitemap $sitemap)
    {
        $this->sitemap = $sitemap;
    }

    public function render($options = array())
    {
        
    }
}
