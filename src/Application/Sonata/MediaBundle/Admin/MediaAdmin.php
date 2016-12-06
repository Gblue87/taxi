<?php
namespace Application\Sonata\MediaBundle\Admin;

use Sonata\MediaBundle\Admin\ORM\MediaAdmin as BaseMediaAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\MediaBundle\Provider\Pool;
use Knp\Menu\ItemInterface as MenuItemInterface;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\MediaBundle\Form\DataTransformer\ProviderDataTransformer;
/**
*
*/
class MediaAdmin extends BaseMediaAdmin
{
    protected $datagridValues = array('_page' => 1, '_sort_order' => 'ASC');

    protected $formOptions = array(
        'validation_groups' => array('newvision')
    );

    protected function configureRoutes(RouteCollection $collection) {
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
            ->add('customDescription')
            ->add('enabled', 'boolean', array('label' => 'list.enabled'))
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
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

        $formMapper->add('translations', 'a2lix_translations', $a2lixFields);
        parent::configureFormFields($formMapper);
        if ($this->getSubject()->getId() !== null) {
            $formMapper
                ->remove('name')
                ->remove('description')
                ->remove('copyright')
                ->remove('cdnIsFlushable')
                ->remove('authorName');
        }
    }
}