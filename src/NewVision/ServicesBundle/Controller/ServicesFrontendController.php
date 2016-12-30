<?php

namespace NewVision\ServicesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Knp\Menu\MenuFactory;
use Knp\Menu\Renderer\ListRenderer;
use Knp\Menu\Matcher\Matcher;
use Knp\Menu\Matcher\Voter\UriVoter;
use NewVision\FrontendBundle\Entity\Order;

class ServicesFrontendController extends Controller
{
    use \NewVision\FrontendBundle\Traits\NewVisionHelperTrait;

    protected $matcher, $router;
    protected $contentPageId             = 9;
    protected $mainRootName              = 'hotels_list';
    protected $servicesCategoriesPerPage = 1000;
    protected $servicesPerPage           = 1000;
    protected $itemsRepo                 = 'NewVisionServicesBundle:Service';
    protected $itemsCategoriesRepo       = 'NewVisionServicesBundle:ServiceCategory';


    /**
     * @Route("/city-and-hotel-transfers{trailingSlash}", name="hotels_list", requirements={"trailingSlash" = "[/]{0,1}"}, defaults={"trailingSlash" = "/"})
     * @Template("NewVisionServicesBundle:Frontend:hotels_list.html.twig")
     */
    public function servicesListAction(Request $request)
    {
        if (substr(urldecode($request->getUri()), -1) != '/') {
            return $this->redirect($request->getUri().'/');
        }
        $em = $this->getDoctrine()->getManager();
        $locale = $request->getLocale();
        $servicesRepo = $em->getRepository($this->itemsRepo);
        $content = $this->getContentPage();

        $query = $servicesRepo->getServicesListingQuery(null, $locale, 1, $this->servicesPerPage);
        //$hotels = new Paginator($query, true);
        $hotels = $query->getResult();

        $this->generateSeoAndOgTags($content);

        return array(
            'hotels'    => $hotels,
            'content'     => $content,
            'breadCrumbs' => $this->generateBreadCrumbs($request),
        );
    }

    /**
     * @Route("/city-and-hotel-transfers/{slug}{trailingSlash}", name="hotel_view", requirements={"trailingSlash" = "[/]{0,1}"}, defaults={"trailingSlash" = "/"})
     * @Template("NewVisionServicesBundle:Frontend:hotelOrder.html.twig")
     */
    public function hotelOrderAction(Request $request, $slug)
    {
        if (substr(urldecode($request->getUri()), -1) != '/') {
            return $this->redirect($request->getUri().'/');
        }
        $to = false;
        $from = false;
        $em = $this->getDoctrine()->getManager();
        $settingsManager = $this->get('newvision.settings_manager');
        $locale = $request->getLocale();
        $servicesRepo = $em->getRepository($this->itemsRepo);
        $point = $request->query->get('point');
        $contentRepository = $em->getRepository('NewVisionContentBundle:Content');
        $hotel = $servicesRepo->findOneBySlugAndLocale($slug, $locale);

        if ($point == 'from') {
            $from = true;
        }else{
            $to = true;
        }
        $terms = $contentRepository->findOneById(24);
        if(!$terms){
            throw $this->createNotFoundException();
        }
        $form = $this->container->get('form.factory')->create('order', new Order(), array(
            'method' => 'POST',
            'action' => $this->generateUrl('hotel_view', array('slug' => $hotel->getSlug())).'/'
        ));

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            $session = $this->get('session');
            if ($form->isValid()) {
                $data = $form->getData();
                $data->setNo(rand(5, 16).time());
                $data->setType('hotel');
                $data->setPaymentType($data->getPaymentType());
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
                    return $this->redirectToRoute('cash_success', array('id' => $data->getNo())).'/';
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

        $dispatcher = $this->get('event_dispatcher');
        $event = new \NewVision\SEOBundle\Event\SeoEvent($hotel);
        if ($event->getOriginalUrl() === null || $event->getOriginalUrl() == '') {
            $event->setOriginalUrl($request->getSchemeAndHttpHost().$this->generateUrl('airport_view', array('slug' => $hotel->getSlug())).'/');
        }
        $dispatcher->dispatch('newvision.seo', $event);

        $this->generateOgTags($hotel);

        $offer['id'] = $hotel->getId();
        return array(
            'hotel' => $hotel,
            'from' => $from,
            'to' => $to,
            'offerPoint' => $point,
            'offer' => json_encode($offer),
            'form' => $form->createView(),
            'terms' => $terms,
            'breadCrumbs' => $this->generateBreadCrumbs($request),
        );
    }
}
