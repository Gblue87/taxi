<?php

namespace NewVision\TinyMCEBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;


class SonataGalleryRestController extends Controller
{
    /**
     * @Route("/rest/all-sonata-galleries", name="rest_sonata_galleries")
     */
    public function restGalleriesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('ApplicationSonataMediaBundle:Gallery');
        $res = $repo->findBy(array('enabled' => 1, 'context' => 'inline_gallery'));
        $results = array();
        foreach ($res as $itm) {
            $results[] = array('value' => $itm->getId(), 'text' => $itm->getName());
        }

    	return new JsonResponse($results);
    }
}