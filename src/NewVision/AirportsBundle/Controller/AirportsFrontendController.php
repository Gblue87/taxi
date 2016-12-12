<?php

namespace NewVision\AirportsBundle\Controller;

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
     * @Route("/airport-transfers", name="airports_list")
     * @Template("NewVisionAirportsBundle:Frontend:airports_list.html.twig")
     */
    public function airportsListAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $locale = $request->getLocale();
        $airportsRepo = $em->getRepository($this->itemsRepo);

        $content = $this->getContentPage();

        $query = $airportsRepo->getAirportsListingQuery(null, $locale, null, null);
        $airports = $query->getResult();

        $this->generateSeoAndOgTags($content);

        return array(
            'airports'    => $airports,
            'content'     => $content
        );
    }

    /**
     * @Route("/airport-transfer/{slug}", name="airport_view")
     * @Template("NewVisionAirportsBundle:Frontend:airportOrder.html.twig")
     */
    public function airportsOrderAction(Request $request, $slug)
    {
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
            'action' => $this->generateUrl('airport_view', array('slug' => $airport->getSlug()))
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
                        'action' => "https://www.sandbox.paypal.com/cgi-bin/webscr",
                        'fields' => array(
                            'cmd' => "_ext-enter",
                            'redirect_cmd' => "_xclick",
                            'business' => 'paypal-facilitator@chestertraveltaxies.co.uk',
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
                    define('WPAY_TEST_MODE',            true);
                    define('WPAY_INSTALLATION_ID',      1110266);
                    define('WPAY_ACCOUNT_ID',           "CHESTERTRAV1M2");
                    define('WPAY_CURRENCY',             "GBP");
                    define('WPAY_MD5_SECRET',           "1Qq!;lgdl;gioijgiojio");
                    define('WPAY_RESPONSE_PASSWORD',    "321W%4fdg5fg/fgfgg");
                    define('WPAY_CART_ID_PREFIX',       "");
                    define('WPAY_INVOICE_ID_ADD',       0);
                    $testMode = WPAY_TEST_MODE ? "100" : "0";
                    $signature = md5(WPAY_MD5_SECRET . ":" . WPAY_CURRENCY . ":$price:$testMode:" . WPAY_INSTALLATION_ID);

                    $worldPay = array(

                        'action' => WPAY_TEST_MODE
                            ? "https://secure-test.worldpay.com/wcc/purchase"
                            : "https://secure.worldpay.com/wcc/purchase",

                        'fields' => array(
                            'instId' => WPAY_INSTALLATION_ID,
                            'amount' => $price,
                            'cartId' => WPAY_CART_ID_PREFIX . ($data->getId() + WPAY_INVOICE_ID_ADD),
                            'currency' => WPAY_CURRENCY,
                            'testMode' => $testMode,
                            'desc' => "TaxiChester Order #$data->getId()",
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

        $this->generateSeoAndOgTags($airport);
        $offer['id'] = $airport->getId();
        return array(
            'airport' => $airport,
            'from' => $from,
            'to' => $to,
            'offerPoint' => $point,
            'offer' => json_encode($offer),
            'form' => $form->createView(),
            'terms' => $terms,
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
     * @Route("/success/{id}", name="cash_success")
     * @Template("NewVisionFrontendBundle:Frontend:cashSuccess.html.twig")
     */
    public function cashSuccessAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $ordersRepository = $em->getRepository('NewVisionFrontendBundle:Order');
        if (!preg_match('/^\d+$/', $id))
            throw $this->createNotFoundException();
        $order = $ordersRepository->findOneByNo($id);
        if (empty($order))
            throw $this->createNotFoundException();

        $this->sendOrderAdminMail($order);
        $this->sendOrderUserMail($order);
        return array(
            'order' => $order,
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
        if (!preg_match('/^\d+$/', $id))
            throw $this->createNotFoundException();
        $order = $ordersRepository->findOneByNo($id);
        if (empty($order))
            throw $this->createNotFoundException();

        if ($order->getPaymentStatus() != 'paid') {
            return $this->redirectToRoute('paypal_error');
        }


        // $this->sendOrderAdminMail($order);
        // $this->sendOrderUserMail($order);
        return array(
            'order' => $order,
        );
    }

    /**
     * @Route("/worldpay-success/{id}", name="worldpay_success")
     * @Template("NewVisionAirportsBundle:Frontend:paypalSuccess.html.twig")
     */
    public function worldpaySuccessAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $ordersRepository = $em->getRepository('NewVisionFrontendBundle:Order');
        if (!preg_match('/^\d+$/', $id))
            throw $this->createNotFoundException();
        $order = $ordersRepository->findOneByNo($id);
        if (empty($order))
            throw $this->createNotFoundException();

        if ($order->getPaymentStatus() != 'paid') {
            return $this->redirectToRoute('paypal_error');
        }


        // $this->sendOrderAdminMail($order);
        // $this->sendOrderUserMail($order);
        return array(
            'order' => $order,
        );
    }

    /**
     * @Route("/paypal-error", name="paypal_error")
     * @Template("NewVisionAirportsBundle:Frontend:paypalError.html.twig")
     */
    public function paypalErrorAction()
    {
        return array(
        );
    }

    /**
     * @Route("/worldpay-error", name="worldpay_error")
     * @Template("NewVisionAirportsBundle:Frontend:paypalError.html.twig")
     */
    public function paypalErrorAction()
    {
        return array(
        );
    }

    /**
     * @Route("/worldpay-notify", name="worldpay_notify")
     */
    public function worldpayNotifyAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $settingsManager = $this->get('newvision.settings_manager');
        $ip = $this->container->get('request')->getClientIp();
        file_put_contents('/home/simplec/taxi/web/test.txt',$ip, FILE_APPEND);
        if (empty($ip) ||
            (substr(gethostbyaddr($ip), -13) != ".worldpay.com")
        )
            return $this->redirectToRoute('worldpay_error');

        $p = $request->request->all();
        $checks = explode(' ', "instId callbackPW AVS cartId currency amount transId transStatus");

        foreach ($checks as $key)
            if (!isset($p[$key]))
                return $this->redirectToRoute('worldpay_error');
        if (
            $p['instId'] != WPAY_INSTALLATION_ID ||
            $p['callbackPW'] != WPAY_RESPONSE_PASSWORD ||
            $p['currency'] != WPAY_CURRENCY ||
            (substr($p['AVS'], 0, 1) != "2" && (!WPAY_TEST_MODE || substr($p['AVS'], 0, 1) != "1")) || !preg_match('/^\d+$/', $p['transId']))
        {
            return $this->redirectToRoute('worldpay_error');
        }
// GET ORDER

        $id = substr($p['cartId'], strlen(WPAY_CART_ID_PREFIX));
        if (!preg_match('/^\d+$/', $id))
            return $this->redirectToRoute('worldpay_error');
        $id -= WPAY_INVOICE_ID_ADD;
        file_put_contents('/home/simplec/taxi/web/test.txt', 'ID:'. $id, FILE_APPEND);

        $order = $em->getRepository('NewVisionFrontendBundle:Order')->findOneByNo($id);

        file_put_contents('/home/simplec/taxi/web/test.txt', 'ORDER NO:'. $order->getNo(), FILE_APPEND);
        if (empty($order) || ($order->getPaymentStatus() != "new"))
            return $this->redirectToRoute('worldpay_error');


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

        file_put_contents('/home/simplec/taxi/web/test.txt', $status, FILE_APPEND);
           $order->setPaymentStatus($status);
           $order->setPaymentTransaction($p['transId']);
           $em->persist($order);
           $em->flush();
            // SEND MAILS IF OK
            if ($status == "paid") {
                $this->sendOrderAdminMail($order);
                $this->sendOrderUserMail($order);
                return $this->redirectToRoute('worldpay_success', array('id' => $order->getId()));
            }
        }
        return $this->redirectToRoute('worldpay_error');
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

        $p = $requestData;
        $status = strtolower($p['payment_status']);

        if (in_array($status, array('denied', 'expired', 'failed'))) {
            $result = self::paypalReturnQuery($p);
            if ($result == 'verified'){
                $order->setPaymentStatus('payment-failed');
                $em->persist($order);
                $em->flush();
            }

        } elseif ($status == "completed") {

            $result = self::paypalReturnQuery($p);


            if ($result == "verified") {
                $status = "paid";

                $price = $order->getAmount() * $settingsManager->get('surcharge');
                if (!$price || $price > (int) $requestData['mc_gross']) {
                    $status = "payment-failed";
                }

                if ($status == "paid") {
                    $this->sendOrderAdminMail($order);
                    $this->sendOrderUserMail($order);
                }

            }else{
                $status = "payment-failed";
            }
            $order->setPaymentStatus($status);
            $order->setPaymentTransaction($p['txn_id']);
            $em->persist($order);
            $em->flush();
        }elseif ($status == "pending") {
            $result = self::paypalReturnQuery($p);
            if ($result == "verified") {
                $status = "paid";

                $price = $order->getAmount() * $settingsManager->get('surcharge');
                if (!$price || $price > (int) $requestData['mc_gross']) {
                    $status = "payment-failed";
                }


                if ($status == "paid") {
                    $this->sendOrderAdminMail($order);
                    $this->sendOrderUserMail($order);
                }

                $order->setPaymentStatus($status);
                $order->setPaymentTransaction($p['txn_id']);
                $em->persist($order);
                $em->flush();
            }
        }
    }


    private static function paypalReturnQuery($p)
    {
        //return "verified";
        try {
            $url = "www.sandbox.paypal.com";
            $url = "https://$url:443/cgi-bin/webscr";

            $p['receiver_email'] = "paypal-facilitator@chestertraveltaxies.co.uk";
            $p['cmd'] = '_notify-validate';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $p);
            curl_setopt($ch, CURLOPT_SSLVERSION, 6);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!

            $result = curl_exec($ch);

            if ($result === false)
                file_put_contents('/home/simplec/taxi/web/test.txt','ERROR: ' . curl_error($ch), FILE_APPEND);

            curl_close($ch);
            if (!$this->checkPaypalTxnId($p['txn_id'])) {
                return 'invalid';
            }

            return strtolower($result);
        } catch (\Exception $e) {
            file_put_contents('/home/simplec/taxi/web/test.txt', $e->getMessage(), FILE_APPEND);
        }
    }

    protected function sendOrderAdminMail($order) {
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
        }
        $adminMessage = \Swift_Message::newInstance()
            ->setSubject($translator->trans('contact.admin_message_subject', array(), 'messages'))
            ->setFrom($settings->get('sender_email'))
            ->setTo($order->getEmail())
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
        $em = $this->getDoctrine()->getManager();
        $ordersRepository = $em->getRepository('NewVisionFrontendBundle:Order');
        $result = $ordersRepository->findOneByPaymentTransaction($id);
        if (!$result) {
            return true;
        }
        return false;
    }

    protected function sendOrderUserMail($order) {
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
        }
        $userMessage = \Swift_Message::newInstance()
            ->setSubject($translator->trans('contact.user_message_subject', array(), 'messages'))
            ->setFrom($settings->get('sender_email'))
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
