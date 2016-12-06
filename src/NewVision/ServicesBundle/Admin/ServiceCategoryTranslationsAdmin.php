<?php

namespace NewVision\ServicesBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;

class ServiceCategoryTranslationsAdmin extends Admin
{
    /**
     * {@inheritdoc}
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('image', 'sonata_type_model_list', array(
                'label' => 'form.image',
                'translation_domain' => 'NewVisionServicesBundle'
            ), array(
                'link_parameters' => array(
                    'context' => 'newvision_service_category_image'
                ))
            )
        ->end();
    }
}
