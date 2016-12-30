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

define('WPAY_TEST_MODE',            false);
define('WPAY_INSTALLATION_ID',      1110266);
define('WPAY_ACCOUNT_ID',           "CHESTERTRAV1M2");
define('WPAY_CURRENCY',             "GBP");
define('WPAY_MD5_SECRET',           "1Qq!;lgdl;gioijgiojio");
define('WPAY_RESPONSE_PASSWORD',    "321W%4fdg5fg/fgfgg");
define('WPAY_CART_ID_PREFIX',       "");
define('WPAY_INVOICE_ID_ADD',       0);

class FrontendController extends Controller
{
    /**
     * @Route("", name="homepage")
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
     * @Route("/new-order{trailingSlash}", name="new_order", requirements={"trailingSlash" = "[/]{0,1}"}, defaults={"trailingSlash" = "/"})
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

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            $settingsManager = $this->get('newvision.settings_manager');
            $session = $this->get('session');
            if ($form->isValid()) {
                $data = $form->getData();
                $data->setNo(rand(5, 16).time());
                $data->setType('');
                $data->setPaymentType($data->getPaymentType());
                $data->setPaymentStatus('new');
                $em->persist($data);
                $em->flush();

                $price = $data->getAmount() * $settingsManager->get('surcharge', 1);
                if(!$price){
                    throw $this->createNotFoundException();
                }
                if ($data->getPaymentType() != null && $data->getPaymentType() == 'paypal') {
                    //LIVE "https://www.paypal.com/cgi-bin/webscr",
                    $paypalForm = array(
                        'action' => "https://www.paypal.com/cgi-bin/webscr",
                        'fields' => array(
                            'cmd' => "_ext-enter",
                            'redirect_cmd' => "_xclick",
                            'business' => 'paypal@taxichester.uk',
                            'invoice' => $data->getNo(),
                            'amount' => $price,
                            'currency_code' => 'GBP',
                            'paymentaction' => "sale",
                            'return' => $request->getSchemeAndHttpHost().$this->generateUrl('paypal_success', array('id' => $data->getNo())),
                            'cancel_return' => $request->getSchemeAndHttpHost().$this->generateUrl('paypal_success', array('id' => $data->getNo())),
                            'notify_url' => $request->getSchemeAndHttpHost().$this->generateUrl('paypal_notify', array('id' => $data->getNo())),
                            'item_name' => "TaxiChester Order #".$data->getNo(),
                            'lc' => "en_GB",
                            'charset' => "utf-8",
                            'no_shipping' => "1",
                            'no_note' => "1",
                            'image_url' => "",
                            'email' => $data->getEmail(),
                            'first_name' => $data->getName(),
                            'last_name' => $data->getFamily(),
                            'custom' => $data->getNo(),
                            'cs' => "0",
                            'page_style' => "PayPal"
                        )
                    );
                    $this->get('session')->set('paypalForm', $paypalForm);
                    return $this->redirectToRoute('paypal_gateway');
                }elseif($data->getPaymentType() != null && $data->getPaymentType() == 'worldpay'){
                    if (empty($data) || ($data->getPaymentStatus() != "new"))
                        throw $this->createNotFoundException();

                    $testMode = WPAY_TEST_MODE ? "100" : "0";
                    $signature = md5(WPAY_MD5_SECRET . ":" . WPAY_CURRENCY . ":$price:$testMode:" . WPAY_INSTALLATION_ID);
                    $worldPay = array(

                        'action' => WPAY_TEST_MODE
                            ? "https://secure-test.worldpay.com/wcc/purchase"
                            : "https://secure.worldpay.com/wcc/purchase",

                        'fields' => array(
                            'instId' => WPAY_INSTALLATION_ID,
                            'amount' => $price,
                            'cartId' => WPAY_CART_ID_PREFIX . ($data->getNo() + WPAY_INVOICE_ID_ADD),
                            'currency' => WPAY_CURRENCY,
                            'testMode' => $testMode,
                            'desc' => "TaxiChester Order #".$data->getNo(),
                            'authMode' => "A",
                            'accId1' => WPAY_ACCOUNT_ID,
                            'withDelivery' => "false",
                            'fixContact' => "false",
                            'hideContact' => "false",
                            'signature' => $signature
                        )
                    );
                    $this->get('session')->set('worldpayForm', $worldPay);
                    return $this->redirectToRoute('worldpay_gateway');
                }elseif($data->getPaymentType() != null && $data->getPaymentType() == 'cash'){
                    $data->setPaymentStatus('cash-order');
                    $em->persist($data);
                    $em->flush();
                    return $this->redirectToRoute('cash_success', array('id' => $data->getNo()));
                }else{
                    throw new \Exception("No payment method found", 404);
                }

            } else {
                $session->getFlashBag()->clear();
                $session->getFlashBag()->add(
                    'error',
                    'applyment_error'
                );
            }
        }

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
            'breadCrumbs' => array($content->getTitle() => null),
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
                'amount4' => $this->_getTaxedPrice($item->getPrice(), $requestData['date'], $requestData['returnDate'], 0),
                'amount8' => $this->_getTaxedPrice($item->getDoublePrice(), $requestData['date'], $requestData['returnDate'], 6),
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

    private function _getTaxedPrice($offerPrice, $date, $returnDate, $passengers)
    {

        $yearDate = date('Y');
        $nextYear = $yearDate+1;
        $specialDates = [$yearDate.'-12-24'=> 2, $yearDate.'-12-25' => 2, $yearDate.'-12-26' => 2, $yearDate.'-12-31' => 2, $nextYear.'-01-01' => 2, $yearDate.'-01-01' => 2];
        $offerPriceTmp = $offerPrice;
        if (in_array($date, array_keys($specialDates))) {
            $offerPrice = $offerPrice * $specialDates[$date];
        }
        $total = $offerPrice;
        if (mb_strlen($returnDate) > 0) {
            if (in_array($returnDate, array_keys($specialDates))) {
                $offerPrice = $offerPriceTmp * $specialDates[$returnDate];
            }else{
                $offerPrice = $offerPriceTmp;
            }
            $total += $offerPrice;

        }
        return $total;
    }

    public function getAmount($requestData, $passengers)
    {
        $yearDate = date('Y');
        $nextYear = $yearDate+1;
        $specialDates = [$yearDate.'-12-24'=> 2, $yearDate.'-12-25' => 2, $yearDate.'-12-26' => 2, $yearDate.'-12-31' => 2, $nextYear.'-01-01' => 2, $yearDate.'-01-01' => 2];
        $settings = $this->get('newvision.settings_manager');
        $night = $settings->get('night');
        $daily = $settings->get('daily');
        $time = date("H", strtotime($requestData['time']));
        if ((int)$time >= 22 || (int)$time < 6) {
           $tariff = $night;
        }else{
           $tariff = $daily;
        }
        if (isset($requestData['returnDate']) && $requestData['returnDate'] != '') {
            $time = date("H", strtotime($requestData['returnTime']));
            if ((int)$time >= 22 || (int)$time < 6) {
               $returnTariff = $night;
            }else{
               $returnTariff = $daily;
            }
        }
        if (in_array($requestData['date'], array_keys($specialDates))) {
            // $pricePerMile = $requestData['']
            $offerPrice = $requestData['distance'] * $specialDates[$requestData['date']] * $tariff;
            if ($requestData['returnDate'] != '') {
                $returnPrice = $requestData['distance'] * $returnTariff;
                if (in_array($requestData['returnDate'], array_keys($specialDates))) {
                    $returnPrice *= $specialDates[$requestData['returnDate']];
                }
                if ($passengers > 4) {
                    $offerPrice += 3;
                }
                $offerPrice += $returnPrice;
            }
            if ($passengers > 4) {
                $offerPrice += 3;
            }
        }else{
            $time = date("H:i", strtotime($requestData['time']));
            $offerPrice = $requestData['distance'] * $tariff;
            if ($requestData['returnDate'] != '') {
                $returnPrice = $requestData['distance'] * $returnTariff;
                if (in_array($requestData['returnDate'], array_keys($specialDates))) {
                    $returnPrice *= $specialDates[$requestData['returnDate']];
                }
                if ($passengers > 4) {
                    $offerPrice += 3;
                }
                $offerPrice += $returnPrice;
            }
            if ($passengers > 4) {
                $offerPrice += 3;
            }

        }
        return $offerPrice;
    }
}
