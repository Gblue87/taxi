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

        $this->generateSeoAndOgTags($hotel);

        $offer['id'] = $hotel->getId();
        return array(
            'hotel' => $hotel,
            'from' => $from,
            'to' => $to,
            'offerPoint' => $point,
            'offer' => json_encode($offer),
        );
    }
}
