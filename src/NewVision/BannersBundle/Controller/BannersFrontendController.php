<?php

namespace NewVision\BannersBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\HttpFoundation\Request;

use NewVision\BannersBundle\Entity\Banners;

class BannersFrontendController extends Controller
{
    /**
     * @Template("NewVisionBannersBundle:Frontend:homepageBanners.html.twig")
     */
    public function homepageBannersAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $banners = $em->getRepository('NewVisionBannersBundle:Banner')->findAllByLocale($request->getLocale());

        return array(
            'banners' => $banners
        );
    }
}
