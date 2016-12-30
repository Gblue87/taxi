<?php

namespace NewVision\AirportsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Knp\Menu\MenuFactory;
use Knp\Menu\Renderer\ListRenderer;
use Knp\Menu\Matcher\Matcher;
use Knp\Menu\Matcher\Voter\UriVoter;
use NewVision\FrontendBundle\Entity\Order;

// define('$this->container->getParameter('WPAY_TEST_MODE');',            true);
// define('$this->container->getParameter('WPAY_INSTALLATION_ID');',      1110266);
// define('$this->container->getParameter('WPAY_ACCOUNT_ID');',           "CHESTERTRAV1M2");
// define('$this->container->getParameter('WPAY_CURRENCY');',             "GBP");
// define('$this->container->getParameter('WPAY_MD5_SECRET');',           "1Qq!;lgdl;gioijgiojio");
// define('$this->container->getParameter('WPAY_RESPONSE_PASSWORD');',    "321W%4fdg5fg/fgfgg");
// define('$this->container->getParameter('WPAY_CART_ID_PREFIX');',       "");
// define('$this->container->getParameter('WPAY_INVOICE_ID_ADD');',       0);

class AirportsFrontendController extends Controller
{
    use \NewVision\FrontendBundle\Traits\NewVisionHelperTrait;

    protected $matcher, $router;
    protected $contentPageId             = 23;
    protected $mainRootName              = 'airports_list';
    protected $airportsCategoriesPerPage = 1000;
    protected $airportsPerPage           = 1000;
    protected $itemsRepo                 = 'NewVisionAirportsBundle:Airport';


    /**
     * @Route("/airport-transfers{trailingSlash}", name="airports_list", requirements={"trailingSlash" = "[/]{0,1}"}, defaults={"trailingSlash" = "/"})
     * @Template("NewVisionAirportsBundle:Frontend:airports_list.html.twig")
     */
    public function airportsListAction(Request $request)
    {
        if (substr(urldecode($request->getUri()), -1) != '/') {
            return $this->redirect($request->getUri().'/');
        }
        $em = $this->getDoctrine()->getManager();
        $locale = $request->getLocale();
        $airportsRepo = $em->getRepository($this->itemsRepo);

        $content = $this->getContentPage();

        $query = $airportsRepo->getAirportsListingQuery(null, $locale, null, null);
        $airports = $query->getResult();

        $this->generateSeoAndOgTags($content);

        return array(
            'airports'    => $airports,
            'content'     => $content,
            'breadCrumbs' => $this->generateBreadCrumbs($request),
        );
    }

    /**
     * @Route("/airport-transfer/{slug}{trailingSlash}", name="airport_view", requirements={"trailingSlash" = "[/]{0,1}"}, defaults={"trailingSlash" = "/"})
     * @Template("NewVisionAirportsBundle:Frontend:airportOrder.html.twig")
     */
    public function airportsOrderAction(Request $request, $slug)
    {
        if (substr(urldecode($request->getUri()), -1) != '/') {
            return $this->redirect($request->getUri().'/');
        }
        $settingsManager = $this->get('newvision.settings_manager');
        $to = false;
        $from = false;
        $em = $this->getDoctrine()->getManager();
        $locale = $request->getLocale();
        $servicesRepo = $em->getRepository($this->itemsRepo);
        $contentRepository = $em->getRepository('NewVisionContentBundle:Content');
        $point = $request->query->get('point');
        $airport = $servicesRepo->findOneBySlugAndLocale($slug, $locale);
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
            'action' => $this->generateUrl('airport_view', array('slug' => $airport->getSlug())).'/'
        ));

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            $session = $this->get('session');
            if ($form->isValid()) {
                $data = $form->getData();
                $data->setNo(rand(5, 16).time());
                $data->setType('airport');
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
                    $testMode = $this->container->getParameter('WPAY_TEST_MODE') ? "100" : "0";
                    $signature = md5($this->container->getParameter('WPAY_MD5_SECRET') . ":" . $this->container->getParameter('WPAY_CURRENCY') . ":$price:$testMode:" . $this->container->getParameter('WPAY_INSTALLATION_ID'));

                    $worldPay = array(

                        'action' => $this->container->getParameter('WPAY_TEST_MODE')
                            ? "https://secure-test.worldpay.com/wcc/purchase"
                            : "https://secure.worldpay.com/wcc/purchase",

                        'fields' => array(
                            'instId' => $this->container->getParameter('WPAY_INSTALLATION_ID'),
                            'amount' => $price,
                            'cartId' => $this->container->getParameter('WPAY_CART_ID_PREFIX') . ($data->getNo() + $this->container->getParameter('WPAY_INVOICE_ID_ADD')),
                            'currency' => $this->container->getParameter('WPAY_CURRENCY'),
                            'testMode' => $testMode,
                            'desc' => "TaxiChester Order #$data->getNo()",
                            'authMode' => "A",
                            'accId1' => $this->container->getParameter('WPAY_ACCOUNT_ID'),
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
            }
        }
        $dispatcher = $this->get('event_dispatcher');
        $event = new \NewVision\SEOBundle\Event\SeoEvent($airport);
        if ($event->getOriginalUrl() === null || $event->getOriginalUrl() == '') {
            $event->setOriginalUrl($request->getSchemeAndHttpHost().$this->generateUrl('airport_view', array('slug' => $airport->getSlug())).'/');
        }
        $dispatcher->dispatch('newvision.seo', $event);

        $this->generateOgTags($airport);
        $offer['id'] = $airport->getId();
        return array(
            'airport' => $airport,
            'from' => $from,
            'to' => $to,
            'offerPoint' => $point,
            'offer' => json_encode($offer),
            'form' => $form->createView(),
            'terms' => $terms,
            'breadCrumbs' => $this->generateBreadCrumbs($request),
        );
    }

    /**
     * @Route("/paypal-gateway", name="paypal_gateway")
     * @Template("NewVisionAirportsBundle:Frontend:gateway.html.twig")
     */
    public function paypalGatewayAction(Request $request){
        $form = $this->get('session')->get('paypalForm');
        return array(
            'form' => $form,
        );
    }

    /**
     * @Route("/worldpay-gateway", name="worldpay_gateway")
     * @Template("NewVisionAirportsBundle:Frontend:gateway.html.twig")
     */
    public function worldpayGatewayAction(Request $request){
        $form = $this->get('session')->get('worldpayForm');
        return array(
            'form' => $form,
        );
    }

    /**
     * @Route("/success/{id}{trailingSlash}", name="cash_success", requirements={"trailingSlash" = "[/]{0,1}"}, defaults={"trailingSlash" = "/"})
     * @Template("NewVisionFrontendBundle:Frontend:cashSuccess.html.twig")
     */
    public function cashSuccessAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $ordersRepository = $em->getRepository('NewVisionFrontendBundle:Order');
        $content = $em->getRepository('NewVisionContentBundle:Content')->findOneById(27);
        if (!preg_match('/^\d+$/', $id))
            throw $this->createNotFoundException();
        $order = $ordersRepository->findOneByNo($id);
        if (empty($order))
            throw $this->createNotFoundException();

        $this->sendOrderAdminMail($order);
        $this->sendOrderUserMail($order);

        $dispatcher = $this->get('event_dispatcher');
        $event = new \NewVision\SEOBundle\Event\SeoEvent($content);
        $dispatcher->dispatch('newvision.seo', $event);

        return array(
            'order' => $order,
            'content' => $content
        );
    }

    /**
     * @Route("/paypal-success/{id}", name="paypal_success")
     * @Template("NewVisionAirportsBundle:Frontend:paypalSuccess.html.twig")
     */
    public function paypalSuccessAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $ordersRepository = $em->getRepository('NewVisionFrontendBundle:Order');
        $content = $em->getRepository('NewVisionContentBundle:Content')->findOneById(27);
        if (!preg_match('/^\d+$/', $id))
            throw $this->createNotFoundException();
        $order = $ordersRepository->findOneByNo($id);
        file_put_contents('/var/www/tax1chester/www/taxi/web/test.txt', 'SUCCESSID: '.$id, FILE_APPEND);
        file_put_contents('/var/www/tax1chester/www/taxi/web/test.txt', print_r($order, true), FILE_APPEND);
        if (empty($order))
            throw $this->createNotFoundException();

        if ($order->getPaymentStatus() != 'paid') {
            return $this->redirectToRoute('paypal_error');
        }

        $dispatcher = $this->get('event_dispatcher');
        $event = new \NewVision\SEOBundle\Event\SeoEvent($content);
        $dispatcher->dispatch('newvision.seo', $event);

        $this->sendOrderAdminMail($order);
        $this->sendOrderUserMail($order);
        return array(
            'order' => $order,
            'content' => $content
        );
    }

    /**
     * @Route("/worldpay-success/{id}{trailingSlash}", name="worldpay_success", requirements={"trailingSlash" = "[/]{0,1}"}, defaults={"trailingSlash" = "/"})
     * @Template("NewVisionAirportsBundle:Frontend:paypalSuccess.html.twig")
     */
    public function worldpaySuccessAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $ordersRepository = $em->getRepository('NewVisionFrontendBundle:Order');
        $content = $em->getRepository('NewVisionContentBundle:Content')->findOneById(27);
        if (!preg_match('/^\d+$/', $id))
            throw $this->createNotFoundException();
        $order = $ordersRepository->findOneByNo($id);
        if (empty($order))
            throw $this->createNotFoundException();

        $dispatcher = $this->get('event_dispatcher');
        $event = new \NewVision\SEOBundle\Event\SeoEvent($content);
        $dispatcher->dispatch('newvision.seo', $event);

        if ($order->getPaymentStatus() != 'paid') {
            return $this->redirectToRoute('paypal_error');
        }


        $this->sendOrderAdminMail($order);
        $this->sendOrderUserMail($order);
        return array(
            'order' => $order,
            'content' => $content
        );
    }

    /**
     * @Route("/paypal-error{trailingSlash}", name="paypal_error", requirements={"trailingSlash" = "[/]{0,1}"}, defaults={"trailingSlash" = "/"})
     * @Template("NewVisionAirportsBundle:Frontend:paypalError.html.twig")
     */
    public function paypalErrorAction(Request $request)
    {
        if (substr(urldecode($request->getUri()), -1) != '/') {
            return $this->redirect($request->getUri().'/');
        }
        return array(
        );
    }

    /**
     * @Route("/worldpay-error/{msg}{trailingSlash}", name="worldpay_error", requirements={"trailingSlash" = "[/]{0,1}"}, defaults={"trailingSlash" = "/"})
     * @Template("NewVisionAirportsBundle:Frontend:paypalError.html.twig")
     */
    public function worldpayErrorAction(Request $request,$msg = null)
    {
        if (substr(urldecode($request->getUri()), -1) != '/') {
            return $this->redirect($request->getUri().'/');
        }
        return array(
            'msg' => $msg,
        );
    }

    /**
     * @Route("/worldpay-notify", name="worldpay_notify")
     */
    public function worldpayNotifyAction(Request $request)
    {
        try {
            $translator = $this->get('translator');
            $em = $this->getDoctrine()->getManager();
            $settingsManager = $this->get('newvision.settings_manager');
            $ip = $this->container->get('request')->getClientIp();
            if (empty($ip) ||
                (substr(gethostbyaddr($ip), -13) != ".worldpay.com")
            )
                return new Response($this->renderView('NewVisionFrontendBundle:Frontend:redirect.html.twig', array('url' => $request->getSchemeAndHttpHost().$this->generateUrl('worldpay_error', array('msg' => $translator->trans('wrong_ip', array(), 'NewVisionFrontendBundle'))))));

            $p = $request->request->all();
            $checks = explode(' ', "instId callbackPW AVS cartId currency amount transId transStatus");

            foreach ($checks as $key)
                if (!isset($p[$key]))
                    return new Response($this->renderView('NewVisionFrontendBundle:Frontend:redirect.html.twig', array('url' => $request->getSchemeAndHttpHost().$this->generateUrl('worldpay_error', array('msg' => $translator->trans('missing_property', array(), 'NewVisionFrontendBundle'))))));
            if (
                $p['instId'] != $this->container->getParameter('WPAY_INSTALLATION_ID') ||
                $p['callbackPW'] != $this->container->getParameter('WPAY_RESPONSE_PASSWORD') ||
                $p['currency'] != $this->container->getParameter('WPAY_CURRENCY') ||
                (substr($p['AVS'], 0, 1) != "2" && (!$this->container->getParameter('WPAY_TEST_MODE') || substr($p['AVS'], 0, 1) != "1")) || !preg_match('/^\d+$/', $p['transId']))
            {
                return new Response($this->renderView('NewVisionFrontendBundle:Frontend:redirect.html.twig', array('url' => $request->getSchemeAndHttpHost().$this->generateUrl('worldpay_error', array('msg' => $translator->trans('wrong_data', array(), 'NewVisionFrontendBundle'))))));
            }
    // GET ORDER

            $id = substr($p['cartId'], strlen($this->container->getParameter('WPAY_CART_ID_PREFIX')));
            if (!preg_match('/^\d+$/', $id))
                return $this->renderView('NewVisionFrontendBundle:Frontend:redirect.html.twig', array('url' => $request->getSchemeAndHttpHost().$this->generateUrl('worldpay_error')));
            $id -= $this->container->getParameter('WPAY_INVOICE_ID_ADD');

            $order = $em->getRepository('NewVisionFrontendBundle:Order')->findOneByNo($id);

            if (empty($order) || ($order->getPaymentStatus() != "new"))
                return new Response($this->renderView('NewVisionFrontendBundle:Frontend:redirect.html.twig', array('url' => $request->getSchemeAndHttpHost().$this->generateUrl('worldpay_error', array('msg' => $translator->trans('wrong_status', array(), 'NewVisionFrontendBundle'))))));


    // TRANSACTION - FAILED
            if (($p['transStatus'] != 'Y')){
                $order->setPaymentStatus('payment-failed');
            }
    // TRANSACTION - SUCCESS
            else {

                // AMOUNT MISSMATCH
                $price = $order->getAmount()*$settingsManager->get('surcharge');
                if (($price === false) ||
                    ((int) $price > (int) $p['amount'])
                )
                    $status = "payment-failed";

                // AMOUNT OK
                else
                    $status = "paid";

               $order->setPaymentStatus($status);
               $order->setPaymentTransaction($p['transId']);
               $em->persist($order);
               $em->flush();

                // SEND MAILS IF OK
                if ($status == "paid") {
                    $this->sendOrderAdminMail($order);
                    $this->sendOrderUserMail($order);
                    return new Response($this->renderView('NewVisionFrontendBundle:Frontend:redirect.html.twig', array('url' => $request->getSchemeAndHttpHost().$this->generateUrl('worldpay_success', array('id' => $order->getNo())))));
                }
            }
            return new Response($this->renderView('NewVisionFrontendBundle:Frontend:redirect.html.twig', array('url' => $request->getSchemeAndHttpHost().$this->generateUrl('worldpay_error'))));
        } catch (\Exception $e) {
            return new Response($this->renderView('NewVisionFrontendBundle:Frontend:redirect.html.twig', array('url' => $request->getSchemeAndHttpHost().$this->generateUrl('worldpay_error', array('msg' => $e->getMessage())))));
        }
    }

    /**
     * @Route("/paypal-notify/{id}", name="paypal_notify")
     */
    public function paypalNotifyAction(Request $request, $id)
    {
        $settingsManager = $this->get('newvision.settings_manager');
        $requestData = $request->request->all();

        if (!isset($requestData['invoice']) || !isset($requestData['payment_status']) || !isset($requestData['mc_gross']))
            return false;
        $em = $this->getDoctrine()->getManager();
        $ordersRepository = $em->getRepository('NewVisionFrontendBundle:Order');
        $order = $ordersRepository->findOneByNo($id);
        file_put_contents('/var/www/tax1chester/www/taxi/web/test.txt', print_r($order, true), FILE_APPEND);

        $p = $requestData;
        $status = strtolower($p['payment_status']);
        file_put_contents('/var/www/tax1chester/www/taxi/web/test.txt', 'STATUS: '.$status, FILE_APPEND);
        if (in_array($status, array('denied', 'expired', 'failed'))) {
            $result = self::paypalReturnQuery($p);
            if ($result == 'verified'){
                $order->setPaymentStatus('payment-failed');
                $em->persist($order);
                $em->flush();
            }

        } elseif ($status == "completed" || $status == 'refunded') {

            $result = self::paypalReturnQuery($p);
            $status = "payment-failed";
            if ($result == "verified") {
                try {
                    $status = "paid";
                    // file_put_contents('/var/www/tax1chester/www/taxi/web/test.txt', 'ORDERTXNID'.$order->getPaymentTransaction(), FILE_APPEND);
                    // if (!$this->checkPaypalTxnId($requestData['txn_id'])) {
                    //     file_put_contents('/var/www/tax1chester/www/taxi/web/test.txt', 'ORDERTXNIDFAILER'.$order->getPaymentTransaction(), FILE_APPEND);
                    //     $status = "payment-failed";
                    // }
                    $price = $order->getAmount() * $settingsManager->get('surcharge');
                    // file_put_contents('/var/www/tax1chester/www/taxi/web/test.txt', 'PRICE1'.$price.'PRICE2'.(float)$requestData['mc_gross'], FILE_APPEND);
                    if (!$price || $price > (float)$requestData['mc_gross']) {
                        $status = "payment-failed";
                    }

                    if ($status == "paid") {
                        $this->sendOrderAdminMail($order);
                        $this->sendOrderUserMail($order);
                    }
                } catch (\Exception $e) {
                    file_put_contents('/var/www/tax1chester/www/taxi/web/test.txt', 'ERRORMSG'.$e->getMessage(), FILE_APPEND);
                }

            }else{
                $status = "payment-failed";
            }
            file_put_contents('/var/www/tax1chester/www/taxi/web/test.txt', 'lastSTATUS'.$status, FILE_APPEND);
            $order->setPaymentStatus($status);
            $order->setPaymentTransaction($p['txn_id']);
            $em->persist($order);
            $em->flush();
        }
    }


    private static function paypalReturnQuery($p)
    {
        //return "verified";
        try {
            $url = "www.paypal.com";
            $url = "https://$url:443/cgi-bin/webscr";

            $p['receiver_email'] = "paypal@taxichester.uk";
            $p['cmd'] = '_notify-validate';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $p);
            curl_setopt($ch, CURLOPT_SSLVERSION, 6);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1); // On dev server only!

            $result = curl_exec($ch);
            curl_close($ch);
            return strtolower($result);
        } catch (\Exception $e) {
            file_put_contents('/var/www/tax1chester/www/taxi/web/test.txt', $e->getMessage(), FILE_APPEND);
        }
    }

    protected function sendOrderAdminMail($order) {
        $settingsManager = $this->get('newvision.settings_manager');
        $translator = $this->get('translator');
        $em =  $this->get('doctrine')->getManager();
        if ($order->getOffer()) {
            $type = $order->getType();
            if ($type == 'airport') {
                $offer = $em->getRepository('NewVisionAirportsBundle:Airport')->findOneById($order->getOffer());
            }elseif ($type == 'hotel') {
                $offer = $em->getRepository('NewVisionServicesBundle:Service')->findOneById($order->getOffer());
            }else{
                $offer = null;
            }
        }else{
            $offer = null;
        }
        $adminMessage = \Swift_Message::newInstance()
            ->setSubject($translator->trans('contact.admin_message_subject', array(), 'NewVisionFrontendBundle'))
            ->setFrom($settingsManager->get('sender_email'))
            ->setTo(explode(',', $settingsManager->get('contact_email')))
            ->setBody(
                $this->renderView(
                    'NewVisionFrontendBundle:Email:admin_payment.html.twig', array(
                        'order' => $order,
                        'offer' => $offer,
                    )
                ),
                'text/html'
            )
        ;

        $mailer = $this->get('mailer');
        $mailer->send($adminMessage);
    }

    public function checkPaypalTxnId($id)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $ordersRepository = $em->getRepository('NewVisionFrontendBundle:Order');
            $result = $ordersRepository->findOneByPaymentTransaction($id);
            file_put_contents('/var/www/tax1chester/www/taxi/web/test.txt', 'CHECKRES: '.print_r($result, true), FILE_APPEND);
            if (!$result) {
                return true;
            }
            return false;
        } catch (\Exception $e) {
            file_put_contents('/var/www/tax1chester/www/taxi/web/test.txt', 'ERRORMSG: '.$e->getMessage(), FILE_APPEND);
        }
    }

    protected function sendOrderUserMail($order) {
        $settingsManager = $this->get('newvision.settings_manager');
        $translator = $this->get('translator');
        $em =  $this->get('doctrine')->getManager();
        if ($order->getOffer()) {
            $type = $order->getType();
            if ($type == 'airport') {
                $offer = $em->getRepository('NewVisionAirportsBundle:Airport')->findOneById($order->getOffer());
            }elseif ($type == 'hotel') {
                $offer = $em->getRepository('NewVisionServicesBundle:Service')->findOneById($order->getOffer());
            }else{
                $offer = null;
            }
        }else{
            $offer = null;
        }
        $userMessage = \Swift_Message::newInstance()
            ->setSubject($translator->trans('contact.user_message_subject', array(), 'NewVisionFrontendBundle'))
            ->setFrom($settingsManager->get('sender_email'))
            ->setTo($order->getEmail())
            ->setBody(
                $this->renderView(
                    'NewVisionFrontendBundle:Email:user_payment.html.twig', array(
                        'order' => $order,
                        'offer' => $offer,
                    )
                ),
                'text/html'
            )
        ;

        $mailer = $this->get('mailer');
        $mailer->send($userMessage);
    }
}
