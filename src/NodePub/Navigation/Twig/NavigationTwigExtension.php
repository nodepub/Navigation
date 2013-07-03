<?php

namespace NodePub\Navigation\Twig;

use NodePub\Navigation\Sitemap;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class NavigationTwigExtension extends \Twig_Extension
{
    private $sitemap;

    protected $twigEnvironment;

    public function __construct(Sitemap $sitemap)
    {
        $this->sitemap = $sitemap;
    }

    public function initRuntime(\Twig_Environment $environment)
    {
        $this->twigEnvironment = $environment;
    }

    public function getName()
    {
        return 'NodePubNavigation';
    }
    
    public function getFunctions()
    {
        return array(
            'breadcrumbs'        => new \Twig_Function_Method($this, 'breadcrumbs'),
        );
    }

    // returns rendered string
    public function breadcrumbs()
    {
    }

    public function breadcrumbLinks()
    {
        return array_reverse($this->sitemap->getActiveNodes());
    }
}
