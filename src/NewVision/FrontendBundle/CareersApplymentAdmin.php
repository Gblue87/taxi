<?php

namespace Stenik\CareersBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Stenik\CareersBundle\Form\Type\JobApplymentType;
use Sonata\AdminBundle\Show\ShowMapper;

class CareersApplymentAdmin extends Admin
{
    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'DESC',
        '_sort_by' => 'id',
    );

    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        $collection->remove('create');
        $collection->remove('edit');
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $jobs = $this->modelManager->createQuery('Stenik\CareersBundle\Entity\Career', 'c' )
            ->orderBy('c.title', 'ASC')
            ->getQuery()
            ->getResult();

        $datagridMapper
            ->add('id')
            ->add('name', null, array('label' => 'form.name'))
            ->add('family', null, array('label' => 'form.family'))
            ->add('applyFor', null, array('label' => 'form.applyFor'));
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name', null, array('label' => 'form.name'))
            ->addIdentifier('family', null, array('label' => 'form.family'))
            ->add('applyFor', null, array('label' => 'form.applyFor'))
            ->add('createdAt', null, array('label' => 'form.createdAt'))
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                ), 'label' => 'actions',
            ))
        ;
    }
    /**
     * Configure the form
     *
     * @param FormMapper $formMapper formMapper
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        $basePath = '/uploads/career-applyment/';

        $cv = $basePath.$this->getSubject()->getCV();
        $motivationLetter = $basePath.$this->getSubject()->getMotivationLetter();
        $photo = $basePath.$this->getSubject()->getPhoto();

        $translator = $this->getConfigurationPool()->getContainer()->get('translator');

        $formMapper
            ->with('General')
                ->add('applyFor', null, array(
                    'required' => false,
                    'label' => 'form.applyFor',
                    'attr' => array(
                        'readonly' => true,
                        'disabled' => true,
                    ),
                 ))
                ->add('name', null, array(
                    'required' => false,
                    'label' => 'careerApply.name',
                    'attr' => array(
                        'readonly' => true,
                        'disabled' => true,
                    ),
                 ))
                ->add('email', null, array(
                    'required' => false,
                    'label' => 'careerApply.email',
                    'attr' => array(
                        'readonly' => true,
                        'disabled' => true,
                    ),
                ))
                ->add('phone', null, array(
                    'required' => false,
                    'label' => 'careerApply.phone',
                    'attr' => array(
                        'readonly' => true,
                        'disabled' => true,
                    ),
                ))
                ->add('cv', null, array(
                    'required' => false,
                    'label' => 'careerApply.cv',
                    'data_class' => null,
                    'mapped' => true,
                    'sonata_help' => $cv != $basePath ? '<a href="'.$cv.'">Свалете CV-то</a>' : '',
                    'attr' => array(
                        'readonly' => true,
                        'disabled' => true,
                    ),
                ))
                ->add('motivationLetter', null, array(
                    'required' => false,
                    'label' => 'careerApply.motivationLetter',
                    'data_class' => null,
                    'mapped' => true,
                    'sonata_help' => $motivationLetter != $basePath ? '<a href="'.$motivationLetter.'">Свалете Мотивационното писмо</a>' : '',
                    'attr' => array(
                        'readonly' => true,
                        'disabled' => true,
                    ),
                ))
                ->add('photo', null, array(
                    'required' => false,
                    'label' => 'careerApply.photo',
                    'data_class' => null,
                    'mapped' => true,
                    'sonata_help' => $photo != $basePath ? '<a href="'.$photo.'">Свалете снимката</a>' : '',
                    'attr' => array(
                        'readonly' => true,
                        'disabled' => true,
                    ),
                ))
            ->end();
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name', null, array('label' => 'form.name', 'translation_domain' => 'messages'))
        ;
    }

    public function getTemplate($name)
    {
        switch ($name) {
            case 'show':
                return 'StenikCareersBundle:Admin:show_action.html.twig';
                break;
            default:
                return parent::getTemplate($name);
                break;
        }
    }
}
