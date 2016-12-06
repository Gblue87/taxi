<?php
namespace NewVision\FrontendBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\File;

class OrderFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setMethod('POST')
            ->add('name', 'text', array(
                'label' => 'career.form.name',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => true,
                'constraints' => new NotBlank(array('message' => 'required_field')),
                'attr' => array(
                    'class' => 'required-entry'
                )
            ))
            ->add('family', 'text', array(
                'label' => 'career.form.family',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => true,
                'constraints' => new NotBlank(array('message' => 'required_field')),
                'attr' => array(
                    'class' => 'required-entry'
                )
            ))
            ->add('phone', 'text', array(
                'label' => 'career.form.phone',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => false,
                'attr' => array(
                    'class' => 'validation-phone'
                )
            ))
            ->add('email', 'email', array(
                'label' => 'career.form.email',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => true,
                'constraints' => new NotBlank(array('message' => 'required_field')),
                'attr' => array(
                    'class' => 'required-entry validation-email'
                )
            ))
            ->add('baggageDetails', 'text', array(
                'label' => 'career.baggageDetails',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => false
            ))
            ->add('no', 'text', array(
                'label' => 'career.no',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => false
            ))
            ->add('info', 'textarea', array(
                'label' => 'career.info',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => false
            ))
            ->add('distance', 'text', array(
                'label' => 'career.distance',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => false
            ))
            ->add('from', 'text', array(
                'label' => 'career.from',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => false
            ))
            ->add('to', 'text', array(
                'label' => 'career.to',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => false
            ))
            ->add('amount', 'text', array(
                'label' => 'career.amount',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => false
            ))
            ->add('start_time', 'text', array(
                'label' => 'career.start_time',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => false
            ))
            ->add('return_time', 'text', array(
                'label' => 'career.return_time',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => false
            ))
            ->add('via', 'text', array(
                'label' => 'career.via',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => false
            ))
            ->add('duration', 'text', array(
                'label' => 'career.duration',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => false
            ))
            ->add('offer', 'text', array(
                'label' => 'career.offer',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => false
            ))
            ->add('from_lat', 'text', array(
                'label' => 'career.from_lat',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => false
            ))
            ->add('from_lng', 'text', array(
                'label' => 'career.from_lng',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => false
            ))
            ->add('to_lat', 'text', array(
                'label' => 'career.to_lat',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => false
            ))
            ->add('to_lng', 'text', array(
                'label' => 'career.to_lng',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => false
            ))
            ->add('via_lat', 'text', array(
                'label' => 'career.via_lat',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => false
            ))
            ->add('via_lng', 'text', array(
                'label' => 'career.via_lng',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => false
            ))
            ->add('date', 'text', array(
                'label' => 'career.date',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => false
            ))
            ->add('return_date', 'text', array(
                'label' => 'career.returnDate',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => false
            ))
            ->add('offer_point', 'text', array(
                'label' => 'career.offer_point',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => false
            ))
            ->add('passengers', 'text', array(
                'label' => 'career.passangers',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => false
            ))
            ->add('type', 'text', array(
                'label' => 'career.type',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => false
            ))
            ->add('paymentTransaction', 'text', array(
                'label' => 'career.paymentTransaction',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => false
            ))
            ->add('paymentType', 'text', array(
                'label' => 'career.paymentType',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => false
            ))
            ->add('submit', 'submit');
    }

    public function getName()
    {
        return 'order';
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaults(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'NewVision\FrontendBundle\Entity\Order',
            'translation_domain' => 'NewVisionFrontendBundle',
        ));
    }
}
