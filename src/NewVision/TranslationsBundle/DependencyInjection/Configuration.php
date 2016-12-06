<?php

namespace NewVision\TranslationsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('newvision_translations');
        $storages = array('orm');
        $registrationTypes = array('all', 'files', 'database');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('fallback_locale')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()

                ->arrayNode('managed_locales')
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('resources_registration')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('type')
                            ->cannotBeEmpty()
                            ->defaultValue('all')
                            ->validate()
                                ->ifNotInArray($registrationTypes)
                                ->thenInvalid('Invalid registration type "%s". Please use one of the following types: '.implode(', ', $registrationTypes))
                            ->end()
                        ->end()
                        ->booleanNode('managed_locales_only')
                            ->defaultTrue()
                        ->end()
                    ->end()
                ->end()

                ->booleanNode('use_yml_tree')
                    ->defaultValue(false)
                ->end()

            ->end()
        ;

        return $treeBuilder;
    }
}
