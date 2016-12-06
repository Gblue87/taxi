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
    protected $mainRootName              = 'services_list';
    protected $servicesCategoriesPerPage = 1000;
    protected $servicesPerPage           = 1000;
    protected $itemsRepo                 = 'NewVisionServicesBundle:Service';
    protected $itemsCategoriesRepo       = 'NewVisionServicesBundle:ServiceCategory';


    /**
     * @Route("/city-and-hotel-transfers/{page}", name="hotels_list", requirements={"page": "\d+"})
     * @Template("NewVisionServicesBundle:Frontend:hotels_list.html.twig")
     */
    public function servicesListAction(Request $request, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();
        $locale = $request->getLocale();
        $servicesRepo = $em->getRepository($this->itemsRepo);
        $content = $this->getContentPage();

        $query = $servicesRepo->getServicesListingQuery(null, $locale, $page, $this->servicesPerPage);
        $hotels = new Paginator($query, true);

        $this->generateSeoAndOgTags($content);

        return array(
            'hotels'    => $hotels,
            'content'     => $content
        );
    }

    /**
     * @Route("/city-and-hotel-transfers/{slug}", name="hotel_view")
     * @Template("NewVisionServicesBundle:Frontend:hotelOrder.html.twig")
     */
    public function hotelOrderAction(Request $request, $slug)
    {
        $to = false;
        $from = false;
        $em = $this->getDoctrine()->getManager();
        $locale = $request->getLocale();
        $servicesRepo = $em->getRepository($this->itemsRepo);
        $point = $request->query->get('point');
        $hotel = $servicesRepo->findOneBySlugAndLocale($slug, $locale);

        if ($point == 'from') {
            $from = true;
        }else{
            $to = true;
        }

        $form = $this->container->get('form.factory')->create('order', new Order(), array(
            'method' => 'POST',
            'action' => $this->generateUrl('hotel_view', array('slug' => $hotel->getSlug()))
        ));

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            $session = $this->get('session');
            if ($form->isValid()) {
                $data = $form->getData();
                $data->setNo(rand(5, 16).time());
                $data->setType('hotel');
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
                        'business' => 'taxichester.uk@gmail.com',
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
                return $this->redirectToRoute('paypal_gateway', array('form' => $paypalForm));

            } else {
                $session->getFlashBag()->clear();
                $session->getFlashBag()->add(
                    'error',
                    'applyment_error'
                );
            }
        }

        $this->generateSeoAndOgTags($hotel);

        $offer['id'] = $hotel->getId();
        return array(
            'hotel' => $hotel,
            'from' => $from,
            'to' => $to,
            'offerPoint' => $point,
            'offer' => json_encode($offer),
            'form' => $form->createView(),
        );
    }
}
