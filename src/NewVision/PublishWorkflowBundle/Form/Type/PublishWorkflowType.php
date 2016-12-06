<?php

namespace NewVision\PublishWorkflowBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PublishWorkflowType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fromDate', 'sonata_type_datetime_picker', array(
                    'format' => 'dd/MM/yyyy HH:mm',
                    'widget' => 'single_text',
                    'dp_side_by_side' => true,
                    'required' => false,
                    'label' => 'form.fromDate',
                ))
                ->add('toDate', 'sonata_type_datetime_picker', array(
                    'format' => 'dd/MM/yyyy HH:mm',
                    'widget' => 'single_text',
                    'dp_side_by_side' => true,
                    'required' => false,
                    'label' => 'form.toDate',
                ))
                ->add('isActive', null, array(
                    'required' => false,
                    'label' => 'form.isActive',
                    'attr' => array(
                        'checked' => $options['is_active'],
                    ),
                ))
                ->add('isHidden', null, array(
                    'required' => false,
                    'label' => 'form.isHidden',
                ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'NewVision\PublishWorkflowBundle\Entity\PublishWorkflow',
            'translation_domain' => 'NewVisionPublishWorkflowBundle',
            'intention'  => 'newvision_publish_workflow',
            'is_active' => true
        ));
    }

    public function getName()
    {
        return 'newvision_publish_workflow';
    }
}
