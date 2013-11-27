<?php

namespace NodePub\Navigation\Twig;

use NodePub\Navigation\Sitemap;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class NavigationTwigExtension extends \Twig_Extension
{
    protected $twigEnvironment,
              $sitemap,
              $twigLoader;

    public function __construct(Sitemap $sitemap)
    {
        $this->sitemap = $sitemap;
    }

    public function initRuntime(\Twig_Environment $environment)
    {
        $this->twigEnvironment = $environment;
        
        $loader = new \Twig_Loader_Filesystem();
        $loader->addPath(__DIR__ . '/../Resorces/views', 'np_navigation');
        
        $environment->getLoader()->addLoader($loader);
    }
    

    public function getName()
    {
        return 'NodePubNavigation';
    }
    
    public function getFunctions()
    {
        return array(
            'breadcrumbs'      => new \Twig_Function_Method($this, 'breadcrumbs'),
            'breadcrumb_links' => new \Twig_Function_Method($this, 'breadcrumbLinks'),
            'menu'             => new \Twig_Function_Method($this, 'menu'),
        );
    }

    public function menu()
    {
        return $this->twigEnvironment->render('@np_navigation\menu.twig', array());
    }

    /**
     * Returns rendered string
     */
    public function breadcrumbs($separator)
    {
        return $this->twigEnvironment->render('@np_navigation\breadcrumbs.twig', array(
            'breadcrumb_items' => $this->sitemap->getActiveNodes()
        ));
    }

    public function breadcrumbLinks()
    {
        return array_reverse($this->sitemap->getActiveNodes());
    }
}
