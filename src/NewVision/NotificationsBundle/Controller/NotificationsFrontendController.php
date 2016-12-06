<?php

namespace NewVision\NotificationsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;

class NotificationsFrontendController extends Controller
{
    public function homepageNotificationsAction(Request $request)
    {
        if ($request->headers->get('referer') == null) {
            $this->get('session')->set('has_referer', false);
        } else {
            $this->get('session')->set('has_referer', true);
        }

        $em = $this->getDoctrine()->getManager();

        $popUp = $em->getRepository('NewVisionNotificationsBundle:Notification')->findOneActiveById(1);

        return $this->render(
            "NewVisionNotificationsBundle:Frontend:notificationsListing.html.twig",
            array(
                'popUp' => $popUp
            )
        );
    }

    /**
     * @Route("/show/notification", name="show_notification")
     */
    public function showNotificationsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $response = new Response();

        $popUp = $em->getRepository('NewVisionNotificationsBundle:Notification')->findOneActiveById(1);

        if ($this->get('session')->get('has_referer') == false) {
            sleep($popUp->getTime() / 1000);
        }

        $now = new \DateTime();
        $cookie = new Cookie('have_to_show_pop_up', 'no', $now->modify('+1 month'));
        $response->headers->setCookie($cookie);
        $response->sendHeaders();

        return $response;
    }
}
