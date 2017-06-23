<?php

namespace NewVision\ContentBundle\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

class DoctrineExtensionListener implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function onLateKernelRequest(GetResponseEvent $event)
    {
        $translatable = $this->container->get('gedmo.listener.translatable');
        $translatable->setTranslatableLocale($event->getRequest()->getLocale());
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $securityContext = $this->container->get('security.context', ContainerInterface::NULL_ON_INVALID_REFERENCE);
        $maintenanceMode = $this->container->get('newvision.settings_manager')->get('maintenance', false);
        $router = $this->container->get('router');
                $this->container->get('session')->set('maintenance', null);
        $allowedIps = explode(',', $this->container->get('newvision.settings_manager')->get('allowed_ips'));
        $ip = $event->getRequest()->getClientIp();
        if($ip == 'unknown'){
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        if($maintenanceMode == 1 && strpos($event->getRequest()->getRequestUri(), 'admin') === false && $this->container->get('session')->get('maintenance', null) === null && (!in_array($ip, $allowedIps))){
                $this->container->get('session')->set('maintenance', true);
                $templating = $this->container->get('templating');
                $content = $this->container->get('doctrine')->getManager()->getRepository('NewVisionContentBundle:Content')->findOneById(29);
                $template = $templating->render('NewVisionFrontendBundle:Frontend:maintenance.html.twig', ['content' => $content, 'breadCrumbs' => [$content->getTitle() => null]]);
                $response = new Response($template, Response::HTTP_SERVICE_UNAVAILABLE, array('Retry-After' => date('F j, Y, G:i', strtotime('+1 hour'))));
                $event->setResponse($response);
                $event->stopPropagation();
        }
        if (null !== $securityContext && null !== $securityContext->getToken() && $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $loggable = $this->container->get('gedmo.listener.loggable');
            $loggable->setUsername($securityContext->getToken()->getUsername());
        }
    }
}