<?php

namespace NewVision\AccentsBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;

class AccentTranslationsAdmin extends Admin
{

    /**
     * {@inheritdoc}
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
        ->add('image', 'sonata_type_model_list', array(
            'label' => 'form.image',
            'translation_domain' => 'NewVisionAccentsBundle'
        ), array(
            'link_parameters' => array(
                'context' => 'newvision_accents'
            ))
        )
        ->end();
    }
}
