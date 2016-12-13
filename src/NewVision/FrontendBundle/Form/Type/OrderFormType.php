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
                'label' => 'order.form.name',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => true,
                'constraints' => new NotBlank(array('message' => 'required_field')),
                'attr' => array(
                    'class' => 'required-entry'
                )
            ))
            ->add('family', 'text', array(
                'label' => 'order.form.family',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => true,
                'constraints' => new NotBlank(array('message' => 'required_field')),
                'attr' => array(
                    'class' => 'required-entry'
                )
            ))
            ->add('phone', 'text', array(
                'label' => 'order.form.phone',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => false,
                'attr' => array(
                    'class' => 'validation-phone'
                )
            ))
            ->add('email', 'email', array(
                'label' => 'order.form.email',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => true,
                'constraints' => new NotBlank(array('message' => 'required_field')),
                'attr' => array(
                    'class' => 'required-entry validation-email'
                )
            ))
            ->add('baggageDetails', 'text', array(
                'label' => 'order.form.baggageDetails',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => false
            ))
            ->add('no', 'text', array(
                'label' => 'order.form.no',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => false
            ))
            ->add('info', 'textarea', array(
                'label' => 'order.form.info',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => false
            ))
            ->add('distance', 'text', array(
                'label' => 'order.form.distance',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => false
            ))
            ->add('from', 'text', array(
                'label' => 'order.form.from',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => false
            ))
            ->add('to', 'text', array(
                'label' => 'order.form.to',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => false
            ))
            ->add('amount', 'text', array(
                'label' => 'order.form.amount',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => false
            ))
            ->add('start_time', 'text', array(
                'label' => 'order.form.start_time',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => false
            ))
            ->add('return_time', 'text', array(
                'label' => 'order.form.return_time',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => false
            ))
            ->add('via', 'text', array(
                'label' => 'order.form.via',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => false
            ))
            ->add('duration', 'text', array(
                'label' => 'order.form.duration',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => false
            ))
            ->add('offer', 'text', array(
                'label' => 'order.form.offer',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => false
            ))
            ->add('from_lat', 'text', array(
                'label' => 'order.form.from_lat',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => false
            ))
            ->add('from_lng', 'text', array(
                'label' => 'order.form.from_lng',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => false
            ))
            ->add('to_lat', 'text', array(
                'label' => 'order.form.to_lat',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => false
            ))
            ->add('to_lng', 'text', array(
                'label' => 'order.form.to_lng',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => false
            ))
            ->add('via_lat', 'text', array(
                'label' => 'order.form.via_lat',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => false
            ))
            ->add('via_lng', 'text', array(
                'label' => 'order.form.via_lng',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => false
            ))
            ->add('date', 'text', array(
                'label' => 'order.form.date',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => false
            ))
            ->add('return_date', 'text', array(
                'label' => 'order.form.returnDate',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => false
            ))
            ->add('offer_point', 'text', array(
                'label' => 'order.form.offer_point',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => false
            ))
            ->add('passengers', 'text', array(
                'label' => 'order.form.passangers',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => false
            ))
            ->add('type', 'text', array(
                'label' => 'order.form.type',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => false
            ))
            ->add('paymentTransaction', 'text', array(
                'label' => 'order.form.paymentTransaction',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => false
            ))
            ->add('meet', 'text', array(
                'label' => 'order.form.meet',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => false
            ))
            ->add('paymentType', 'text', array(
                'label' => 'order.form.paymentType',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => false
            ))
            ->add('captcha', 'ds_re_captcha', array('mapped' => false))
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
