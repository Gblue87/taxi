<?php

namespace NewVision\AirportsBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;

class AirportsTranslationsAdmin extends Admin
{

    /**
     * {@inheritdoc}
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('image', 'sonata_type_model_list', array(
                'label' => 'form.image',
                'translation_domain' => 'NewVisionAirportsBundle'
            ), array(
                'link_parameters' => array(
                    'context' => 'newvision_services_image'
                ))
            )
            ->add('gallery', 'sonata_type_model_list', array(
                'label' => 'form.gallery',
                'translation_domain' => 'NewVisionAirportsBundle'
            ), array(
                'link_parameters' => array(
                    'context' => 'newvision_services_gallery'
                ))
            )
        ->end();
    }
}
