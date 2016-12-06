<?php

namespace NewVision\TranslationsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Translator compiler pass to automatically pass loader to the other services.
 *
 * @author Cédric Girard <c.girard@lexik.fr>
 */
class TranslatorPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        // loaders
        $loaders = array();
        $loadersReferences = array();

        foreach ($container->findTaggedServiceIds('translation.loader') as $id => $attributes) {
            $loaders[$id][] = $attributes[0]['alias'];
            $loadersReferences[$attributes[0]['alias']] = new Reference($id);

            if (isset($attributes[0]['legacy-alias'])) {
                $loaders[$id][] = $attributes[0]['legacy-alias'];
                $loadersReferences[$attributes[0]['legacy-alias']] = new Reference($id);
            }
        }

        if ($container->hasDefinition('newvision_translations.translator')) {
            $container->findDefinition('newvision_translations.translator')->replaceArgument(2, $loaders);
        }

        if ($container->hasDefinition('newvision_translations.importer.file')) {
            $container->findDefinition('newvision_translations.importer.file')->replaceArgument(0, $loadersReferences);
        }

        // exporters
        if ($container->hasDefinition('newvision_translations.exporter_collector')) {
            foreach ($container->findTaggedServiceIds('newvision_translations.exporter') as $id => $attributes) {
                $container->getDefinition('newvision_translations.exporter_collector')->addMethodCall('addExporter', array($id, new Reference($id)));
            }
        }
    }
}