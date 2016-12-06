<?php

namespace NewVision\TinyMCEBundle\DependencyInjection;

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
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $rootNode = $treeBuilder->root('new_vision_tiny_mce')
            ->children()
                ->scalarNode('mode')->defaultValue("exact")->end()
                ->scalarNode('selector')->defaultValue('textarea.tinymce')->end()
                ->scalarNode('language')->defaultValue('bg_BG')->end()
                ->scalarNode('theme')->defaultValue('modern')->end()
                ->scalarNode('height')->defaultValue('500px')->end()
                ->scalarNode('theme_advanced_toolbar_location')->defaultValue('top')->end()
                ->scalarNode('theme_advanced_toolbar_align')->defaultValue('left')->end()
                ->scalarNode('theme_advanced_statusbar_location')->defaultValue('bottom')->end()
                ->booleanNode('theme_advanced_resizing')->defaultTrue()->end()
                ->booleanNode('image_advtab')->defaultTrue()->end()
                ->booleanNode('save_enablewhendirty')->defaultTrue()->end()
                ->scalarNode('toolbar')->defaultValue('insertfile undo redo | fontselect | fontsizeselect | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | forecolor backcolor emoticons | link image | preview media fullpage newvisionGallery')->end()
                ->scalarNode('fontsize_formats')->defaultValue("8px 10px 12px 14px 18px 24px 36px")->end()
                ->scalarNode('font_formats')->defaultValue('Andale Mono=andale mono,times;Arial=arial,helvetica,sans-serif;Arial Black=arial black,avant garde;Book Antiqua=book antiqua,palatino;Comic Sans MS=comic sans ms,sans-serif;Courier New=courier new,courier;Georgia=georgia,palatino;Helvetica=helvetica;Impact=impact,chicago;Symbol=symbol;Tahoma=tahoma,arial,helvetica,sans-serif;Terminal=terminal,monaco;Times New Roman=times new roman,times;Trebuchet MS=trebuchet ms,geneva;Verdana=verdana,geneva;Webdings=webdings;Wingdings=wingdings,zapf dingbats;')->end()
                ->arrayNode('textcolor_map')
                    ->prototype('array')
                        ->prototype('variable')->end()
                    ->end()
                    ->defaultValue(array(
                        "000000", "Black", "993300", "Burnt orange", "333300", "Dark olive", "003300", "Dark green", "003366", "Dark azure", "000080", "Navy Blue", "333399", "Indigo", "333333", "Very dark gray", "800000", "Maroon", "FF6600", "Orange", "808000", "Olive", "008000", "Green", "008080", "Teal", "0000FF", "Blue", "666699", "Grayish blue", "808080", "Gray", "FF0000", "Red", "FF9900", "Amber", "99CC00", "Yellow green", "339966", "Sea green", "33CCCC", "Turquoise", "3366FF", "Royal blue", "800080", "Purple", "999999", "Medium gray", "FF00FF", "Magenta", "FFCC00", "Gold", "FFFF00", "Yellow", "00FF00", "Lime", "00FFFF", "Aqua", "00CCFF", "Sky blue", "993366", "Red violet", "FFFFFF", "White", "FF99CC", "Pink", "FFCC99", "Peach", "FFFF99", "Light yellow", "CCFFCC", "Pale green", "CCFFFF", "Pale cyan", "99CCFF", "Light sky blue", "CC99FF", "Plum"
                    ))
                ->end()
                ->scalarNode('textcolor_rows')->defaultValue(5)->end()
                ->scalarNode('entity_encoding')->defaultValue('raw')->end()
                ->scalarNode('init_instance_callback')->defaultValue('')->end()
                ->scalarNode('body_class')->defaultValue('')->end()
                ->scalarNode('removed_menuitems')->defaultValue('newdocument, print')->end()
                ->scalarNode('file_browser_callback')->defaultValue('ajaxfilemanager')->end()
                ->scalarNode('language')->defaultValue('bg_BG')->end()
                ->scalarNode('newvision_gallery_rest')->defaultValue('/rest/all-sonata-galleries')->end()
                ->scalarNode('content_css')->defaultValue('')->end()
                ->variableNode('image_class_list')->defaultValue('')->end()
                ->variableNode('link_class_list')->defaultValue('')->end()
                ->variableNode('table_class_list')->defaultValue('')->end()
                ->variableNode('extended_valid_elements')->defaultValue('')->end()
                ->variableNode('valid_children')->defaultValue('')->end()
                ->arrayNode('plugins')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->useAttributeAsKey('name')
                        ->prototype('variable')->end()
                    ->end()
                    ->defaultValue(array(
                        "advlist autolink link image lists charmap preview hr anchor pagebreak",
                        "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
                        "table contextmenu directionality emoticons template paste textcolor code newvisionGallery",
                    ))
                ->end()
                ->arrayNode('menu')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->useAttributeAsKey('name')
                        ->prototype('variable')->end()
                    ->end()
                    ->defaultValue(array(
                        "file" => array('title' => 'File', 'items' => 'newdocument'),
                        "edit" => array('title' => 'Edit', 'items' => 'undo redo  | cut copy paste selectall | searchreplace'),
                        "insert" => array('title' => 'Insert', 'items' => 'link media | template hr'),
                        "view" => array('title' => 'View', 'items' => 'visualaid'),
                        "format" => array('title' => 'Format', 'items' => 'bold italic underline strikethrough superscript subscript | formats | removeformat'),
                        "table" => array('title' => 'Table', 'items' => 'inserttable tableprops deletetable | cell row column'),
                        "tools" => array('title' => 'Tools', 'items' => 'spellchecker code'),
                    ))
                ->end()
                // Configure external TinyMCE plugins
                ->arrayNode('external_plugins')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('url')->isRequired()->end()
                        ->end()
                    ->end()
                ->end()
                ->booleanNode('relative_urls')->defaultFalse()->end()
            ->end()
        ->end();

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        return $treeBuilder;
    }
}
