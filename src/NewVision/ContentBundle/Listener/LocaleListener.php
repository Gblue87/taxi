<?php

namespace NewVision\ContentBundle\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LocaleListener implements ContainerAwareInterface
{
	    protected $container;

    public function __construct(\Symfony\Component\DependencyInjection\Container $container)
    {
        $this->container = $container;
    }

    public function Myfunction()
    {
        $languages = $this->container->getParameter('languages');
    }
}