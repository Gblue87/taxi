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
                $em->persist($data);
                $em->flush();

                $price = $data->getAmount() * $settingsManager->get('surcharge', 1);
                if(!$price){
                    throw $this->createNotFoundException();
                }
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

        // $this->sendOrderAdminMail($order);
        // $this->sendOrderUserMail($order);
        return array(
            'order' => $order,
        );
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
            try {
                $result = self::paypalReturnQuery($p);
            } catch (\Exception $e) {

            }

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
                $em->persist($order);
                $em->flush();
            }
        }elseif ($status == "pending") {

            try {
                $result = self::paypalReturnQuery($p);
            } catch (\Exception $e) {

            }

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
                $em->persist($order);
                $em->flush();
            }
        }
    }


    private static function paypalReturnQuery($p)
    {
        //return "verified";
        try {

            $req = 'cmd=_notify-validate';
            foreach ($p as $key => $value) {

              $value = urlencode(stripslashes($value));
              $value = preg_replace('/(.*[^%^0^D])(%0A)(.*)/i','${1}%0D%0A${3}',$value);// IPN fix
              $req .= "&$key=$value";
            }
            file_put_contents('/home/simplec/taxi/web/test.txt', $req, FILE_APPEND);



            // assign posted variables to local variables

            $data['item_name']          = $p['item_name'];
            $data['item_number']        = $p['item_number'];
            $data['payment_status']     = $p['payment_status'];
            $data['payment_amount']     = $p['mc_gross'];
            $data['payment_currency']   = $p['mc_currency'];
            $data['txn_id']             = $p['txn_id'];
            $data['receiver_email']     = $p['receiver_email'];
            $data['payer_email']        = $p['payer_email'];
            $data['custom']             = $p['custom'];



            // post back to PayPal system to validate

            $header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
            $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
            $header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
            $fp = fsockopen ('ssl://www.sandbox.paypal.com', 443, $errno, $errstr, 30);



            if (!$fp) {
            } else {
                fputs($fp, $header . $req);

                while (!feof($fp)) {
                    $res = fgets ($fp, 1024);
                    file_put_contents('/home/simplec/taxi/web/test.txt', $res, FILE_APPEND);
                    if (strcmp($res, "VERIFIED") == 0) {
                          // Used for debugging
                          // mail('user@domain.com', 'PAYPAL POST - VERIFIED RESPONSE', print_r($post, true));
                          // Validate payment (Check unique txnid &amp;amp;amp; correct price)
                          // PAYMENT VALIDATED &amp;amp;amp; VERIFIED!
                    } else if (strcmp ($res, "INVALID") == 0) {
                         // PAYMENT INVALID &amp;amp;amp; INVESTIGATE MANUALY!

                         // E-mail admin or alert user



                         // Used for debugging

                         //@mail("user@domain.com", "PAYPAL DEBUGGING", "Invalid Response
                    }
                }
                fclose ($fp);
            }
    } catch (\Exception $e) {
        file_put_contents('/home/simplec/taxi/web/test.txt', $e->getMessage(), FILE_APPEND);
    }

        return trim(strtolower($result));
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
