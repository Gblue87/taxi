<?php
namespace NewVision\SEOBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * A form type for editing the SEO metadata.
 *
 * @author Hrist Hristoff <hristo.hristov@newvision.bg>
 */
class MetaDataValueType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('property', 'text', array('required' => false))
            ->add('value', 'text', array('required' => false))
        ;
    }
    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'translation_domain' => 'NewVisionSEOBundle',
            'required' => false,
        ));
    }
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'meta_data_value';
    }
}