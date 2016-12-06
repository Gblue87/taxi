<?php
namespace NewVision\ContactsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Email;

class ContactsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setMethod('POST')
            ->add('name', 'text', array(
                'label' => 'contact.name',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => true,
                'constraints' => new NotBlank(array('message' => 'required_field')),
                'attr' => array(
                    'class' => 'required-entry'
                    //'placeholder' => 'contact.name'
                )
            ))
            ->add('phone', 'text', array(
                'label' => 'contact.phone',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => false,
                'constraints' => array(
                    new Regex(array(
                        'pattern' => '/^(0|\+)[0-9]+$/',
                        'match' => true,
                        'message' => 'only_numbers',
                    )),
                    //new NotBlank(array('message' => 'required_field')),
                ),
                'attr' => array(
                    'class' => 'validation-phone'
                    //'placeholder' => 'contact.phone'
                )
            ))
            ->add('email', 'text', array(
                'label' => 'contact.email',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => true,
                'constraints' => array(
                    new NotBlank(array('message' => 'required_field')),
                    new Email()
                ),
                'attr' => array(
                    'class' => 'required-entry validation-email'
                    //'placeholder' => 'contact.email'
                )
            ))
            ->add('subject', 'text', array(
                'label' => 'contact.subject',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => true,
                'constraints' => new NotBlank(array('message' => 'required_field')),
                'attr' => array(
                    'class' => 'required-entry'
                    //'placeholder' => 'contact.name'
                )
            ))
            ->add('message', 'textarea', array(
                'label' => 'contact.message',
                'translation_domain' => 'NewVisionFrontendBundle',
                'required' => true,
                'constraints' => new NotBlank(array('message' => 'required_field')),
                'attr' => array(
                    'class' => 'required-entry',
                    //'placeholder' => 'contact.message',
                    'cols' => 30,
                    'rows' => 10
                )
            ))
            ->add('captcha', 'ds_re_captcha', array('mapped' => false, 'attr' => array('class' => 'ffield g-recaptcha')))
            ->add('submit', 'submit');
    }

    public function getName()
    {
        return 'contacts';
    }
}
