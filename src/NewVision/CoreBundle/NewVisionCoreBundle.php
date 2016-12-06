<?php

namespace NewVision\CoreBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use NewVision\CoreBundle\DependencyInjection\Compiler\SonataRoutePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class NewVisionCoreBundle extends Bundle
{
	/**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new SonataRoutePass());
    }
}
