<?php
namespace NewVision\CoreBundle\DependencyInjection\Compiler;


use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class SonataRoutePass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        foreach ($container->findTaggedServiceIds('sonata.admin') as $id => $tags) {
            $tags = array_pop($tags);
            if(isset($tags['audit']) && $tags['audit']) {
                $definition = $container->getDefinition($id);
                $definition->addMethodCall('setRouteBuilder', array(new Reference('newvision.core.route.entity')));
            }
        }
    }
}