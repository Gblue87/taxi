<?php

namespace NewVision\ServicesBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;

class ServicesTranslationsAdmin extends Admin
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
                    'context' => 'newvision_services_image'
                ))
            )
            ->add('gallery', 'sonata_type_model_list', array(
                'label' => 'form.gallery',
                'translation_domain' => 'NewVisionServicesBundle'
            ), array(
                'link_parameters' => array(
                    'context' => 'newvision_services_gallery'
                ))
            )
        ->end();
    }
}
