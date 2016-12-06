<?php

namespace NewVision\BannersBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;

class BannerTranslationsAdmin extends Admin
{

    /**
     * {@inheritdoc}
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
        ->add('image', 'sonata_type_model_list', array(
            'label' => 'form.image',
            'translation_domain' => 'NewVisionBannersBundle'
        ), array(
            'link_parameters' => array(
                'context' => 'newvision_banners'
            ))
        )
        ->end();
    }
}
