<?php

namespace NewVision\SettingsBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class SettingAdmin extends Admin
{
    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        // $collection->remove('delete');
        $collection->remove('export');
        $sc =  $this->getConfigurationPool()->getContainer()->get('security.context');
    }

    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $options = array('label' => 'settings.name');
        if ($this->id($this->getSubject())) {
            $options['read_only'] = true;
        }
        $formMapper
            ->add('label', null, array('label' => 'settings.label'))
            ->add('name', 'text', $options)
            ->add('value', null, array('label' => 'settings.value'))
            ->add('visible', null, array('label' => 'settings.visible'))
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('label')
            ->add('value')
        ;
    }

    public function getBatchActions()
    {
        // retrieve the default (currently only the delete action) actions
        $actions = parent::getBatchActions();

        $sc =  $this->getConfigurationPool()->getContainer()->get('security.context');
        if (!$sc->isGranted('ROLE_SUPER_ADMIN')) {
            unset($actions['delete']);
        }

        return $actions;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $sc =  $this->getConfigurationPool()->getContainer()->get('security.context');

        if ($sc->isGranted('ROLE_SUPER_ADMIN')) {
            $listMapper
                ->addIdentifier('label', null, array('editable' => true, 'label' => 'settings.label'))
                ->add('name', null, array('editable' => true, 'label' => 'settings.name'))
                ->add('value', null, array('editable' => true, 'label' => 'settings.value'))
                ->add('visible', null, array('editable' => true, 'label' => 'settings.visible'))
                ->add('_action', 'actions', array(
                    'actions' => array(
                        'delete' => array(),
                        'edit' => array(),
                    ),
                    'label' => 'settings.actions',
                ))
            ;
        } else {
            $listMapper
                ->addIdentifier('label', null, array('label' => 'settings.label'))
                ->add('value', null, array('editable' => true, 'label' => 'settings.value'))
            ;
        }
    }

    /**
     * Hide the non-visible records from other than ROLE_SUPER_ADMIN
     * @param  string $context
     * @return object \Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery
     */
    public function createQuery($context = 'list')
    {
        $query = parent::createQuery($context);
        $sc =  $this->getConfigurationPool()->getContainer()->get('security.context');
        if (!$sc->isGranted('ROLE_SUPER_ADMIN')) {
            $query->andWhere(
                $query->expr()->eq($query->getRootAlias().'.visible', ':visible')
            );

            $query->setParameter('visible', '1');
        }

        return $query;
    }
}
