<?php
/**
 * This file is part of the NewVisionMailChimpBundle.
 *
 * (c) Nikolay Tumbalev <n.tumbalev@newvision.bg>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NewVision\MailChimpBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Validator\ErrorElement;

class MailChimpAdmin extends Admin
{
    protected function configureTabMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        parent::configureTabMenu($menu, $action, $childAdmin);

        if ($action == 'history') {
            $id = $this->getRequest()->get('id');
            $menu->addChild(
                "General",
                array('uri' => $this->generateUrl('history', array('id' => $id)))
            );

            $locales = $this->getConfigurationPool()->getContainer()->getParameter('locales');

            foreach ($locales as $value) {
                $menu->addChild(
                    strtoupper($value),
                    array('uri' => $this->generateUrl('history', array('id' => $id, 'locale' => $value)))
                );
            }
        }
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        $collection->remove('history', $this->getRouterIdParameter().'/history');
        $collection->remove('history_view_revision', $this->getRouterIdParameter().'/preview/{revision}');
        $collection->remove('history_revert_to_revision', $this->getRouterIdParameter().'/revert/{revision}');
        $collection->remove('order', 'order');
        $collection->remove('create');
        $collection->remove('delete');
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('title', null, array('label' => 'form.title'))
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('title', null, array('label' => 'form.title'))
            ->add('isActive', null, array('label' => 'form.isActive', 'editable' => true))
            ->add('createdAt', null, array('label' => 'form.created_at'))
            ->add('_action', 'actions', array(
                'actions' => array(
                    'edit' => array(),
                    'delete' => array(),
                    'history' => array('template' => 'NewVisionCoreBundle:Admin:list_action_history.html.twig'),
                ), 'label' => 'table.label_actions',
            ))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $mailChimpObject = $this->getSubject();

        $lists = array();
        $apiKey = $mailChimpObject->getApiKey();

        if ($apiKey) {
            try {
                $mc = new \NewVision\MailChimpBundle\Services\MailChimp($apiKey);

                $mailChimpLists = $mc->getList()->lists();

                foreach ($mailChimpLists->lists as $key => $list) {
                    $lists[$list->id] = $list->name;
                }
            } catch (\Exception $e) {

            }
        }

        $formMapper
            ->with('tab.general', array(
                'tab' => true,
            ))
                ->with('form.general', array(
                        'class' => 'col-md-12',
                        'label' => 'form.general',
                        'translation_domain' => 'NewVisionMailChimpBundle',
                    )
                )
                    ->add('title', null, array('label' => 'form.title'))
                    ->add('apiKey', null, array('label' => 'form.apiKey', 'error_bubbling' => true))
                    ->add('isActive', null, array('label' => 'form.isActive', 'required' => false))
                    ->add('doubleOptin', null, array('label' => 'form.doubleOptin', 'required' => false))
                    ->add('listId', 'choice', array('label' => 'form.listId', 'required' => false, 'choices' => $lists))
                ->end()
            ->end();
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        if (strpos($object->getApiKey(), '-') == false) {
            $error = 'API Key error!';
            $errorElement->with('apiKey')->addViolation($error)->end();
            $this->getRequest()->getSession()->getFlashBag()->add( "menu_type_check", $error );
        }

    }
}
