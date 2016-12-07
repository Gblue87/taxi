<?php

namespace NewVision\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

use JMS\Payment\CoreBundle\Plugin\Exception\Action\VisitUrl;
use JMS\Payment\CoreBundle\Plugin\Exception\ActionRequiredException;
use JMS\Payment\CoreBundle\PluginController\Result;
use JMS\Payment\CoreBundle\Form\ChoosePaymentMethodType;
use NewVision\FrontendBundle\Entity\Order;

class FrontendController extends Controller
{
    /**
     * @Route("/", name="homepage", defaults={"_locale"="en"})
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $locale = $request->getLocale();
        $content = $em->getRepository('NewVisionContentBundle:Content')->findOneById(1);
        if (!$content) {
            throw $this->createNotFoundException('The page is not found');
        }
        $sliderBlock = $em->getRepository('NewVisionCustomBlocksBundle:CustomBlock')->findOneByIdAndLocale(7, $locale);
        $aboutUsBlock = $em->getRepository('NewVisionCustomBlocksBundle:CustomBlock')->findOneByIdAndLocale(8, $locale);
        $aboutUs = $em->getRepository('NewVisionContentBundle:Content')->findOneById(25);
        if(!$aboutUs){
            throw $this->createNotFoundException('About us page not found');
        }

        $airports = $em->getRepository('NewVisionAirportsBundle:Airport')->findHomepageAirports($locale);
        $hotels = $em->getRepository('NewVisionServicesBundle:Service')->findHomepageHotels($locale);

        $dispatcher = $this->get('event_dispatcher');
        $event = new \NewVision\SEOBundle\Event\SeoEvent($content);
        $dispatcher->dispatch('newvision.seo', $event);

        $this->get('newvision.og_tags')->loadOgTags($content);

        return array(
            'content' => $content,
            'aboutUs' => $aboutUs,
            'hotels' => $hotels,
            'airports' => $airports,
            'sliderBlock' => $sliderBlock,
            'aboutUsBlock' => $aboutUsBlock,
        );
    }

    /**
     * @Template("NewVisionFrontendBundle:Frontend:footer.html.twig")
     */
    public function renderFooterAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $settings = $this->container->get('newvision.settings_manager');
        $bundles = $this->container->getParameter('kernel.bundles');
        $locale = $request->getLocale();

        $footerBlock1 = $em->getRepository('NewVisionCustomBlocksBundle:CustomBlock')->findOneByIdAndLocale(2, $locale);
        $footerBlock2 = $em->getRepository('NewVisionCustomBlocksBundle:CustomBlock')->findOneByIdAndLocale(3, $locale);
        $footerBlock3 = $em->getRepository('NewVisionCustomBlocksBundle:CustomBlock')->findOneByIdAndLocale(4, $locale);
        $footerBlock4 = $em->getRepository('NewVisionCustomBlocksBundle:CustomBlock')->findOneByIdAndLocale(6, $locale);
        $footerBlocks = array(
            $footerBlock1 != null ? $footerBlock1->getRank() : '-1' => $footerBlock1,
            $footerBlock2 != null ? $footerBlock2->getRank() : '-2' => $footerBlock2,
            $footerBlock3 != null ? $footerBlock3->getRank() : '-3' => $footerBlock3,
        );

        ksort($footerBlocks);

        $footerText = $em->getRepository('NewVisionCustomBlocksBundle:CustomBlock')->findOneByIdAndLocale(5, $locale);

        return array(
            'settings'     => $settings,
            'footerBlocks' => $footerBlocks,
            'footerText'   => $footerText,
            'footerBlock4' => $footerBlock4,
        );
    }

    /**
     * @Route("/new-order", name="new_order")
     * @Template("NewVisionFrontendBundle:Frontend:newOrder.html.twig")
     */
    public function renderNewOrderAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $contentRepository = $em->getRepository('NewVisionContentBundle:Content');

        $requestData = $request->request->all();

        $fields = explode('|', "from|to|date_submit|time_submit|passengers|date2_submit|time2_submit|vias_location");

        $form = $this->container->get('form.factory')->create('order', new Order(), array(
            'method' => 'POST',
            'action' => $this->generateUrl('new_order')
        ));

        $fill = array();
        foreach ($fields as $field)
            if (isset($requestData[$field]) && strlen($requestData[$field]))
                $fill[$field] = $requestData[$field];

        $content = $contentRepository->findOneById(5);
        $terms = $contentRepository->findOneById(24);
        if(!$content || !$terms){
            throw $this->createNotFoundException();
        }

        $dispatcher = $this->get('event_dispatcher');
        $event = new \NewVision\SEOBundle\Event\SeoEvent($content);
        $dispatcher->dispatch('newvision.seo', $event);

        $this->get('newvision.og_tags')->loadOgTags($content);

        return array(
            'content' => $content,
            'fill' => json_encode($fill),
            'form' => $form->createView(),
            'terms' => $terms,
        );
    }

    /**
     * @Route("/order", name="order")
     * @Template("NewVisionFrontendBundle:Frontend:order.html.twig")
     */
    public function renderOrderAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $contentRepository = $em->getRepository('NewVisionContentBundle:Content');

        $requestData = $request->request->all();

        $fields = explode('|', "from|to|date_submit|time_submit|passengers|date2_submit|time2_submit|vias_location");

        $fill = array();
        foreach ($fields as $field)
            if (isset($requestData[$field]) && strlen($requestData[$field]))
                $fill[$field] = $requestData[$field];

        $content = $contentRepository->findOneById(5);
        if(!$content){
            throw $this->createNotFoundException();
        }

        $dispatcher = $this->get('event_dispatcher');
        $event = new \NewVision\SEOBundle\Event\SeoEvent($content);
        $dispatcher->dispatch('newvision.seo', $event);

        $this->get('newvision.og_tags')->loadOgTags($content);

        return array(
            'content' => $content,
            'fill' => json_encode($fill),
        );
    }

    /**
     * @Template("NewVisionFrontendBundle:Frontend:header.html.twig")
     */
    public function renderHeaderAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $settings = $this->container->get('newvision.settings_manager');
        $route = $request->attributes->get('route');
        if ($route == null) {
            $route = $request->attributes->get('_route');
        }
        $routeParams = $request->attributes->get('route_params');
        if ($routeParams == null) {
            $routeParams = $request->attributes->get('_route_params');
        }

        $currLocale = $request->getLocale();
        $locales = $this->container->getParameter('locales');

        $urls = array();
        $itm = null;
        if ($route != null) {
            if (isset($routeParams['slug'])) {
                $itm = $this->findObject($route, $routeParams['slug'], $currLocale, $em);
            }

            if (isset($routeParams['categorySlug'])) {
                $itm = $this->findObject($route, $routeParams['categorySlug'], $currLocale, $em);
            }

            foreach ($locales as $key) {
                if ($itm != null) {
                    $trans = $itm->getTranslations()->get($key);
                    if ($trans != null && ($localeSlug = $trans->getSlug())) {
                        if (isset($routeParams['categorySlug'])) {
                            $urls[$key] = $this->generateUrl($route, array_merge($routeParams, array('locale' => $key, 'categorySlug' => $localeSlug)));
                        } else {
                            $urls[$key] = $this->generateUrl($route, array_merge($routeParams, array('locale' => $key, 'slug' => $localeSlug)));
                        }
                    } else {
                        $urls[$key] = $this->generateUrl($route, array_merge($routeParams, array('locale' => $key)));
                    }
                } else {
                    foreach ($routeParams as $k => $v) {
                        if ($k[0] === '_') {
                            unset($routeParams[$k]);
                        }
                    }
                    $urls[$key] = $this->generateUrl($route, array_merge($routeParams, array('_locale' => $key)));
                }
            }
        }

        $headerText = $em->getRepository('NewVisionCustomBlocksBundle:CustomBlock')->findOneByIdAndLocale(1, $request->getLocale());

        return $this->render("NewVisionFrontendBundle:Frontend:header.html.twig", array(
            'locales'    => $locales,
            'urls'       => $urls,
            'settings'   => $settings,
            'headerText' => $headerText,
        ));
    }

    private function findObject($route, $slug, $locale, $em, $specific = null)
    {
        if ($route == 'content') {
            $repo = $em->getRepository('NewVisionContentBundle:Content');

            return $repo->findOneBySlugAndLocale($slug, $locale);
        } elseif ($route == 'news_view') {
            $repo = $em->getRepository('NewVisionNewsBundle:News');

            return $repo->findOneBySlugAndLocale($slug, $locale);
        } elseif ($route == 'product') {
            $repo = $em->getRepository('NewVisionProductsBundle:Product');

            return $repo->findOneBySlugAndLocale($slug, $locale);
        } elseif ($route == 'products_categories_category_view') {
            $repo = $em->getRepository('NewVisionProductsBundle:ProductCategory');

            return $repo->findOneBySlugAndLocale($slug, $locale);
        } elseif ($route == 'service') {
            $repo = $em->getRepository('NewVisionServicesBundle:Service');

            return $repo->findOneBySlugAndLocale($slug, $locale);
        } elseif ($route == 'service_category') {
            $repo = $em->getRepository('NewVisionServicesBundle:ServiceCategory');

            return $repo->findOneBySlugAndLocale($slug, $locale);
        } elseif ($route == 'dealer_view') {
            $repo = $em->getRepository('NewVisionDealersBundle:Dealer');

            return $repo->findOneBySlugAndLocale($slug, $locale);
        } elseif ($route == 'career_view') {
            $repo = $em->getRepository('NewVisionCareersBundle:Career');

            return $repo->findOneBySlugAndLocale($slug, $locale);
        }

        return;
    }

    /**
     * @Template("NewVisionFrontendBundle:Frontend:403.html.twig")
     */
    public function custom403Action()
    {
        $em = $this->getDoctrine()->getManager();
        $content = $em->getRepository("NewVisionContentBundle:Content")->findOneById(19);

        if (!$content) {
            throw $this->createNotFoundException("Page not found");
        }

        $breadCrumbs = array(
            $content->getTitle() => null,
        );

        $dispatcher = $this->get('event_dispatcher');
        $event = new \NewVision\SEOBundle\Event\SeoEvent();
        $event->setTitle($content->getTitle());
        $dispatcher->dispatch('newvision.seo', $event);

        return array(
            'content' => $content,
            'breadCrumbs' => $breadCrumbs,
        );
    }

    /**
     * @Template("NewVisionFrontendBundle:Frontend:404.html.twig")
     */
    public function custom404Action()
    {
        $em = $this->getDoctrine()->getManager();
        $content = $em->getRepository("NewVisionContentBundle:Content")->findOneById(2);

        if (!$content) {
            throw $this->createNotFoundException("Page not found");
        }

        $breadCrumbs = array(
            $content->getTitle() => null,
        );

        $dispatcher = $this->get('event_dispatcher');
        $event = new \NewVision\SEOBundle\Event\SeoEvent();
        $event->setTitle($content->getTitle());
        $dispatcher->dispatch('newvision.seo', $event);

        return array(
            'content' => $content,
            'breadCrumbs' => $breadCrumbs,
        );
    }

    /**
     * @Template("NewVisionFrontendBundle:Frontend:503.html.twig")
     */
    public function custom503Action()
    {
        $em = $this->getDoctrine()->getManager();
        $content = $em->getRepository("NewVisionContentBundle:Content")->findOneById(20);

        if (!$content) {
            throw $this->createNotFoundException("Page not found");
        }

        $breadCrumbs = array(
            $content->getTitle() => null,
        );

        $dispatcher = $this->get('event_dispatcher');
        $event = new \NewVision\SEOBundle\Event\SeoEvent();
        $event->setTitle($content->getTitle());
        $dispatcher->dispatch('newvision.seo', $event);

        return array(
            'content' => $content,
            'breadCrumbs' => $breadCrumbs,
        );
    }

    /**
     * @Route("/offer-amount", name="offer_amount", defaults={"_locale"="en"})
     */
    public function getOfferAmount(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $data = [];
            $requestData = $request->request->all();
            if (
                !isset($requestData['offer']) ||
                !isset($requestData['date']) ||
                !isset($requestData['passengers']) ||
                !isset($requestData['returnDate']) ||
                !preg_match('/^\d+$/', $requestData['offer']) ||
                !preg_match('/^\d+$/', $requestData['passengers'])
            ){
                $data['success'] = false;
            }
            if ($requestData['offerType'] == 'hotel') {
                $item = $em->getRepository('NewVisionServicesBundle:Service')->findOneById($requestData['offer']);
            }else{
                $item = $em->getRepository('NewVisionAirportsBundle:Airport')->findOneById($requestData['offer']);
            }

            $data = array(
                'amount4' => $this->_getTaxedPrice($item->getPrice(), $requestData['date'], $requestData['returnDate']),
                'amount8' => $this->_getTaxedPrice($item->getDoublePrice(), $requestData['date'], $requestData['returnDate']),
                'success' => true
            );
            if (isset($requestData['meet']) && $requestData['meet']) {
                if ($requestData['meet'] == 'true') {
                    $meet = (int)$this->get('newvision.settings_manager')->get('meet_and_greet', 0);
                    $data['amount4'] += $meet;
                    $data['amount8'] += $meet;
                }
            }
            $data['amount'] = ($requestData['passengers'] > 4)
                ? $data['amount8']
                : $data['amount4'];
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/distance-amount", name="distance_amount")
     */
    public function getDistanceAmount(Request $request){
        $requestData = $request->request->all();
         if (!isset($requestData['distance']) ||
            !isset($requestData['date']) ||
            !isset($requestData['passengers']) ||
            !isset($requestData['returnDate']) ||
            !preg_match('/^\d+$/', $requestData['passengers']) ||
            (
                strlen($requestData['returnDate'])
            )
        ){
            $data['success'] = false;
        }

        $data = array(
            'amount4' => number_format($this->getAmount($requestData, 4), 2, '.', ''),
            'amount8' => number_format($this->getAmount($requestData, 8), 2, '.', '')
        );
        if (isset($requestData['meet']) && $requestData['meet']) {
            if ($requestData['meet'] == 'true') {
                $meet = (int)$this->get('newvision.settings_manager')->get('meet_and_greet', 0);
                $data['amount4'] += $meet;
                $data['amount8'] += $meet;
            }
        }
        $data['amount'] = ($requestData['passengers'] > 4)
            ? $data['amount8']
            : $data['amount4'];

        return new JsonResponse($data);
    }

    private function _getTaxedPrice($offerPrice, $date, $returnDate)
    {
        $yearDate = date('Y');
        $nextYear = $yearDate+1;
        $specialDates = [$yearDate.'-12-24'=> 1.5, $yearDate.'-12-25' => 2, $yearDate.'-12-26' => 2, $yearDate.'-12-31' => 1.5, $nextYear.'-01-01' => 2, $yearDate.'-01-01' => 2];

        if (in_array($date, array_keys($specialDates))) {
            $offerPrice = $offerPrice * $specialDates[$date];
        }
        $total = $offerPrice;
        if (mb_strlen($returnDate) > 0) {
            if (in_array($returnDate, array_keys($specialDates))) {
                $offerPrice = $offerPrice * $specialDates[$returnDate];
            }
            $total += $offerPrice;
        }
        return $total;
    }

    public function getAmount($requestData, $passengers)
    {
        $yearDate = date('Y');
        $nextYear = $yearDate+1;
        $specialDates = [$yearDate.'-12-24'=> 1.5, $yearDate.'-12-25' => 2, $yearDate.'-12-26' => 2, $yearDate.'-12-31' => 1.5, $nextYear.'-01-01' => 2, $yearDate.'-01-01' => 2];
        $settings = $this->get('newvision.settings_manager');
        $night = $settings->get('night');
        $daily = $settings->get('daily');
        $time = date("H", strtotime($requestData['time']));
        if ((int)$time >= 22 || (int)$time < 6) {
           $tariff = $night;
        }else{
           $tariff = $daily;
        }
        if (in_array($requestData['date'], array_keys($specialDates))) {
            // $pricePerMile = $requestData['']
            $offerPrice = $requestData['distance'] * $specialDates[$date] * $tariff;
            if ($passengers > 4) {
                $offerPrice += 3;
            }
        }else{
            $time = date("H:i", strtotime($requestData['time']));
            $offerPrice = $requestData['distance'] * $tariff;
            if ($passengers > 4) {
                $offerPrice += 3;
            }

        }
        return $offerPrice;
    }
}
