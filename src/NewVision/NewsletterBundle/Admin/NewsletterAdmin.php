<?php
namespace NewVision\NewsletterBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Validator\ErrorElement;
use Knp\Menu\ItemInterface as MenuItemInterface;

class NewsletterAdmin extends Admin
{
    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'DESC',
        '_sort_by' => 'createdAt',
    );

    protected function configureTabMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        parent::configureTabMenu($menu, $action, $childAdmin);

        if ($action == 'list') {
            $menu->addChild(
                'MailChimpSync',
                array('uri' => $this->generateUrl('mail-chimp-status'))
            );
        }
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        $collection->add('mail-chimp-status', 'mail-chimp-status');
        $collection->remove('edit');
        $collection->remove('create');
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('email', null, array('label' => 'form.email'))
        ;
    }

    /**
     * Configure the list
     *
     * @param \Sonata\AdminBundle\Datagrid\ListMapper $list list
     */
    protected function configureListFields(ListMapper $list)
    {
        $container = $this->getConfigurationPool()->getContainer();
        $bundles = $container->getParameter('kernel.bundles');
        if (array_key_exists('NewVisionMailChimpBundle', $bundles)) {
            $list
            ->addIdentifier('email', null, array('label' => 'form.email'))
            ->add('mailChimpStatus', null, array('label' => 'form.mailChimpStatus'))
            ->add('createdAt', null, array('label' => 'form.createdAt'))
            ->add('_action', 'actions', array(
                'actions' => array(
                    'delete' => array(),
                ),
                'label' => 'actions'
            ))
            ;
        } else {
            $list
            ->addIdentifier('email', null, array('label' => 'form.email'))
            ->add('createdAt', null, array('label' => 'form.createdAt'))
            ->add('_action', 'actions', array(
                'actions' => array(
                    'delete' => array(),
                ),
                'label' => 'actions'
            ))
            ;
        }
    }

    /**
     * Configure the form
     *
     * @param FormMapper $formMapper formMapper
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General')
                ->add('email', 'text', array(
                    'required' => false,
                    'label' => 'form.email'
                ))
            ->end();
    }

    public function preRemove($item)
    {
        $container = $this->getConfigurationPool()->getContainer();
        $bundles = $container->getParameter('kernel.bundles');
        if (array_key_exists('NewVisionMailChimpBundle', $bundles)) {
            $em = $container->get('doctrine')->getManager();
            $mailChimpObject = $em->getRepository('NewVisionMailChimpBundle:MailChimp')->findOneById(1);
            if ($mailChimpObject != null && $mailChimpObject->getListId() != null && $mailChimpObject->getListId() != '') {
                $mc = new \NewVision\MailChimpBundle\Services\MailChimp($mailChimpObject->getApiKey());
                $editMember = $mc->getList($mailChimpObject->getListId())->editListMember($item->getEmail(), 'unsubscribed');
            }
        }
    }
}
