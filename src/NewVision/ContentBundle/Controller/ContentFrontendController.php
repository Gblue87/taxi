<?php

namespace NewVision\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use NewVision\ContentBundle\Entity\Content;
use Knp\Menu\MenuFactory;
use Knp\Menu\Renderer\ListRenderer;
use Knp\Menu\Matcher\Matcher;
use Knp\Menu\Matcher\Voter\UriVoter;

class ContentFrontendController extends Controller
{
    /**
     * @Route("/terms-and-conditions{trailingSlash}", name="terms-and-conditions", requirements={"trailingSlash" = "[/]{0,1}"}, defaults={"trailingSlash" = "/"})
     * @Template("NewVisionContentBundle:Frontend:index.html.twig")
     */
    public function termsAndConditionsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $content = $em->getRepository('NewVisionContentBundle:Content')->findOneById(24);
        if (!$content) {
            throw $this->createNotFoundException("Page not found");
        }

        $breadCrumbs = array($content->getTitle() => null);

        $dispatcher = $this->get('event_dispatcher');
        $event = new \NewVision\SEOBundle\Event\SeoEvent($content);
        $dispatcher->dispatch('newvision.seo', $event);

            $params = array();

        $this->get('newvision.og_tags')->loadOgTags($content, $params);

        return array(
            'content'     => $content,
            'breadCrumbs' => $breadCrumbs,
        );
    }

    /**
     * @Route("/sitemap{trailingSlash}", name="sitemap", requirements={"trailingSlash" = "[/]{0,1}"}, defaults={"trailingSlash" = "/"})
     * @Template("NewVisionContentBundle:Frontend:sitemap.html.twig")
     */
    public function sitemapAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $locale  = $request->getLocale();
        $sitemapContent = $em->getRepository('NewVisionContentBundle:Content')->findOneById(3);
        $breadCrumbs = array($sitemapContent->getTitle() => null);

        $bundles = $this->container->getParameter('kernel.bundles');
        if (array_key_exists('NewVisionContentBundle', $bundles)) {
            $content = $em->getRepository('NewVisionContentBundle:Content')->findAllByLocale($locale);
        }
        if (array_key_exists('NewVisionCareersBundle', $bundles)) {
            $careers = $em->getRepository('NewVisionCareersBundle:Career')->findAllByLocale($locale);
        }
        if (array_key_exists('NewVisionProductsBundle', $bundles)) {
            $products = $em->getRepository('NewVisionProductsBundle:Product')->findAllByLocale($locale);
        }
        if (array_key_exists('NewVisionServicesBundle', $bundles)) {
            $services = $em->getRepository('NewVisionServicesBundle:Service')->findAllByLocale($locale);
        }
        if (array_key_exists('NewVisionBrandsBundle', $bundles)) {
            $brands = $em->getRepository('NewVisionBrandsBundle:Brand')->findAllByLocale($locale);
        }

        $dispatcher = $this->get('event_dispatcher');
        $event = new \NewVision\SEOBundle\Event\SeoEvent($sitemapContent);
        $dispatcher->dispatch('newvision.seo', $event);


        $params = array();


        $this->get('newvision.og_tags')->loadOgTags($sitemapContent, $params);

        return array(
            'content'        => $content,
            'sitemapContent' => $sitemapContent,
            'careers'        => $careers,
            'products'       => $products,
            'services'       => $services,
            'brands'         => $brands,
            'breadCrumbs'    => $breadCrumbs,
        );
    }

    private function createMenu($children, $menu, $repo, $locale)
    {
        foreach ($children as $itm) {
            $subMenu = $menu->addChild($itm->getTitle(), array('uri' => $itm->getSlug(), 'currentClass' => 'selected'));
            if ($itm->getSlug() === '/' && $itm->getSlug() == $this->container->get('request')->getRequestUri()) {
                $subMenu->setAttribute('class', 'selected');
            } elseif ($itm->getSlug() !== '/' && strpos($this->container->get('request')->getRequestUri(), $itm->getSlug()) !== false) {
                $subMenu->setAttribute('class', 'selected');
            }
            if (count($children = $repo->findAllChildren($itm->getId(), $locale))) {
                $subMenu->setAttribute('class', $subMenu->getAttribute('class').' hasDropdown');
                $subMenu->setChildrenAttribute('class', 'dropdown');
                $this->createMenu($children, $subMenu, $repo, $locale);
            }
        }
    }

    /**
     * @Route("/{slug}{trailingSlash}", name="content", requirements={"trailingSlash" = "[/]{0,1}"}, defaults={"trailingSlash" = "/"})
     * @Template("NewVisionContentBundle:Frontend:index.html.twig")
     */
    public function indexAction(Request $request, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        $settings = $this->get('newvision.settings_manager');
        $locale = $request->getLocale();

        $repo = $em->getRepository('NewVisionContentBundle:Content');
        $content = $repo->findOneBySlugAndLocale($slug, $locale);
        if (!$content) {
            throw $this->createNotFoundException("Page not found");
        }

        $children = $repo->findAllChildren($content->getId(), $locale);
        //if page have no description try to find children with description and redirect to it
        if (!$content->getDescription() && !$content->getParent()) {
            foreach ($children as $contentChild) {
                if ($contentChild->getDescription()) {
                    return $this->redirect($this->generateUrl('content', array('slug' => $contentChild->getSlug())));
                }
            }
        }

        //root is the parent
        $root = $content->getParent();

        //have no parent
        if ($root == null) {
            //check for children
            if (count($children) > 0) {
                //content is root
                $root = $content;
            }
        } else {
            //have parent, so we search for the root
            while ($root->getParent() != null) {
                $root = $root->getParent();
            }
        }

        //if there are root we build side menu
        $sideBar = null;
        if ($root != null) {
            $menuChildrens = $repo->findAllChildren($root->getId(), $locale);
            $factory = new MenuFactory();
            $sideBar = $factory->createItem('root', array());

            $this->router = $this->container->get("router");
            $this->matcher = new Matcher();
            $this->matcher->addVoter(new UriVoter($_SERVER['REQUEST_URI']));
            $this->createMenu($menuChildrens, $sideBar, $repo, $locale);
            $renderer = new ListRenderer(new \Knp\Menu\Matcher\Matcher());
        }

        $breadCrumbs = array();
        foreach ($repo->getPath($content) as $object) {
            if ($object->getSlug() != null) {
                $breadCrumbs[$object->getTitle()] = $this->generateUrl('content', array('slug' => $object->getSlug()));
            }
        }

        $dispatcher = $this->get('event_dispatcher');
        $event = new \NewVision\SEOBundle\Event\SeoEvent($content);
        $dispatcher->dispatch('newvision.seo', $event);


        $params = array();


        $this->get('newvision.og_tags')->loadOgTags($content, $params);

        return array(
            'content'     => $content,
            'children'    => $children,
            'breadCrumbs' => $breadCrumbs,
            'settings'    => $settings,
            'sideBar'     => $sideBar != null ? $renderer->render($sideBar, array('currentClass' => 'selected', 'ancestorClass'=>'selected')) : false,
        );
    }
}
