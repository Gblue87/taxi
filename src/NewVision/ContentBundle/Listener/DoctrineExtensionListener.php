<?php

namespace NewVision\ContentBundle\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

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
        if($maintenanceMode == 1 && strpos($event->getRequest()->getRequestUri(), 'admin') === false && $this->container->get('session')->get('maintenance', null) === null){
                $this->container->get('session')->set('maintenance', true);
                $response = new RedirectResponse($router->generate('maintenance'));
                $event->setResponse($response);
        }
        if (null !== $securityContext && null !== $securityContext->getToken() && $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $loggable = $this->container->get('gedmo.listener.loggable');
            $loggable->setUsername($securityContext->getToken()->getUsername());
        }
    }
}