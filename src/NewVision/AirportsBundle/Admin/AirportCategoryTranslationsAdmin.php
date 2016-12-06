<?php

namespace NewVision\AirportsBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;

class AirportCategoryTranslationsAdmin extends Admin
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
        ->end();
    }
}
