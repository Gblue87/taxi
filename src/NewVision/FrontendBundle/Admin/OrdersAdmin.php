<?php

namespace NewVision\FrontendBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use NewVision\FrontendBundle\Form\Type\OrderFormType;
use Sonata\AdminBundle\Show\ShowMapper;

class OrdersAdmin extends Admin
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
        $jobs = $this->modelManager->createQuery('NewVision\FrontendBundle\Entity\Order', 'c' )
            ->orderBy('c.id', 'DESC')
            ->getQuery()
            ->getResult();

        $datagridMapper
            ->add('id')
            ->add('name', null, array('label' => 'form.name'))
            ->add('family', null, array('label' => 'form.family'));
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name', null, array('label' => 'form.name'))
            ->addIdentifier('family', null, array('label' => 'form.family'))
            ->add('createdAt', null, array('label' => 'form.createdAt'))
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                ), 'label' => 'actions',
            ))
        ;
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
                return 'NewVisionFrontendBundle:Admin:show_action.html.twig';
                break;
            default:
                return parent::getTemplate($name);
                break;
        }
    }
}
