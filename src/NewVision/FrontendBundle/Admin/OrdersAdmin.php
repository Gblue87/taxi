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
            ->add('family', null, array('label' => 'form.family'))
            ->add('from', null, array('label' => 'From'))
            ->add('to', null, array('label' => 'To'))
            ->add('date', null, array('label' => 'Date'))
            ->add('start_time', null, array('label' => 'Start time'))
            ->add('passengers', null, array('label' => 'Passengers'))
            ->add('distance', null, array('label' => 'Distance'))
            ->add('amount', null, array('label' => 'Amount'))
            ->add('phone', null, array('label' => 'Phone'))
            ->add('email', null, array('label' => 'Email'));
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('no', 'string', array('label' => 'ID'))
            ->addIdentifier('from', null, array('label' => 'From'))
            ->addIdentifier('to', null, array('label' => 'To'))
            ->addIdentifier('date', null, array('label' => 'Date'))
            ->addIdentifier('start_time', null, array('label' => 'Start time'))
            ->addIdentifier('passengers', null, array('label' => 'Passengers'))
            ->addIdentifier('distance', null, array('label' => 'Distance'))
            ->addIdentifier('amount', null, array('label' => 'Amount'))
            ->addIdentifier('name', null, array('label' => 'Name'))
            ->addIdentifier('family', null, array('label' => 'Family'))
            ->addIdentifier('phone', null, array('label' => 'Phone'))
            ->addIdentifier('email', null, array('label' => 'Email'))
            ->addIdentifier('paymentStatus', null, array('label' => 'Payment status'))
            ->addIdentifier('paymentType', null, array('label' => 'Payment type'))
            ->addIdentifier('newMeet', 'boolean', array('label' => 'Meet and greet'))
            ->add('createdAt', null, array('label' => 'createdAt'))
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
