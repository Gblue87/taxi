<?php

namespace NewVision\TranslationsBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use NewVision\TranslationsBundle\DependencyInjection\Compiler\TranslatorPass;

class NewVisionTranslationsBundle extends Bundle
{
	public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new TranslatorPass());
    }
}
