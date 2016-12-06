<?php

namespace Application\Sonata\MediaBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\MediaBundle\Provider\Pool;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\MediaBundle\Admin\GalleryAdmin as BaseGalleryAdmin;

class GalleryAdmin extends BaseGalleryAdmin
{
    protected $datagridValues = array('_page' => 1, '_sort_order' => 'ASC', '_sort_by' => 'id');

    protected $formOptions = array(
        'validation_groups' => array('newvision'),
    );

    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        $collection->add('order', 'order');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('title')
            ->add('enabled', null)
            #->add('context', 'trans', array('catalogue' => 'SonataMediaBundle'))
            #->add('defaultFormat', 'trans', array('catalogue' => 'SonataMediaBundle'))
            ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        // define group zoning
        $formMapper
            ->with($this->trans('Gallery'), array('class' => 'col-md-9'))->end()
            ->with($this->trans('Options'), array('class' => 'col-md-3'))->end();

        $context = $this->getPersistentParameter('context');

        if (!$context) {
            $context = $this->pool->getDefaultContext();
        }

        $formats = array();
        foreach ((array) $this->pool->getFormatNamesByContext($context) as $name => $options) {
            $formats[$name] = $name;
        }

        $contexts = array();
        foreach ((array) $this->pool->getContexts() as $contextItem => $format) {
            $contexts[$contextItem] = $contextItem;
        }

         $a2lixFields = array(
            'fields' => array(
                'title' => array(
                    'field_type' => 'text',
                    'label' => 'form.title',
                    'translation_domain' => 'SonataMediaBundle',
                    'required' => true
                ),
                'customDescription' => array(
                    'field_type' => 'textarea',
                    'label' => 'form.customDescription',
                    'translation_domain' => 'SonataMediaBundle',
                    'required' => false,
                    'attr' => array(
                        'class' => 'tinymce',
                        'data-theme' => 'bbcode'
                    )
                )
            ),
            'label' => 'form.translations'
        );

        $a2lixFields['fields']['customDescription']['display'] = false;

        $formMapper
            ->with('Options')
                ->add('enabled', null, array('required' => false))
                ->add('translations', 'a2lix_translations', $a2lixFields)
                ->add('defaultFormat', 'hidden', array('attr' => array('value' => array_pop($formats))))
            ->end()
            ->with('Gallery')
                ->add('galleryHasMedias', 'sonata_type_collection', array(
                        'cascade_validation' => true,
                    ), array(
                        'edit'              => 'inline',
                        'inline'            => 'table',
                        'sortable'          => 'position',
                        'link_parameters'   => array('context' => $context),
                        'admin_code'        => 'sonata.media.admin.gallery_has_media'
                    )
                )
            ->end()
        ;
    }
}
