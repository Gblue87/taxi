<?php
namespace NewVision\SEOBundle\Form\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use NewVision\SEOBundle\Form\EventListener\MetaDataListener;
/**
 * A form type for editing the SEO metadata.
 *
 * @author Hrist Hristoff <hristo.hristov@newvision.bg>
 */
class MetaDataType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('translations', 'a2lix_translations', array(
                'fields' => array(
                    'title' => array(
                        'label' => 'form.title',
                        'required' => false,
                    ),
                    'description' => array(
                        'label' => 'form.description',
                        'required' => false,
                    ),
                    'keywords' => array(
                        'label' => 'form.keywords',
                        'required' => false,
                    ),
                    'originalUrl' => array(
                        'label' => 'form.originalUrl',
                        'required' => false,
                    ),
                ),
                'exclude_fields' => array(
                    'extraProperties',
                    'extraNames',
                    'extraHttp',
                ),
                'translation_domain' => 'NewVisionSEOBundle',
                'label' => 'form.translations',
            ))
        ;
    }
    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => '\NewVision\SEOBundle\Entity\MetaData',
            'translation_domain' => 'NewVisionSEOBundle',
            'required' => false,
        ));
    }
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'meta_data';
    }
}