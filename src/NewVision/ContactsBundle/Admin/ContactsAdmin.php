<?php
/**
 * This file is part of the NewVisionContactsBundle.
 *
 * (c) Nikolay Tumbalev <n.tumbalev@newvision.bg>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NewVision\ContactsBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 *  Custom admin class
 *
 * @package NewVisionContactsBundle
 * @author  Nikolay Tumbalev <n.tumbalev@newvision.bg>
 */
class ContactsAdmin extends Admin
{
    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'DESC',
        '_sort_by' => 'createdAt',
    );

    public function getBatchActions()
    {
        $actions = parent::getBatchActions();

        $actions['hide'] = [
            'label'            => $this->trans('action_hide', array(), 'NewVisionCoreBundle'),
            'ask_confirmation' => true, // If true, a confirmation will be asked before performing the action
        ];
        $actions['show'] = [
            'label'            => $this->trans('action_show', array(), 'NewVisionCoreBundle'),
            'ask_confirmation' => true, // If true, a confirmation will be asked before performing the action
        ];

        return $actions;
    }

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
        $collection->remove('edit');
        $collection->remove('delete');
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('name', null, array('label' => 'form.name'))
            ->add('family', null, array('label' => 'form.family'))
            ->add('phone', null, array('label' => 'form.phone'))
            ->add('email', null, array('label' => 'form.email'))
            ->add('commingFrom', null, array('label' => 'form.commingFrom'))
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('createdAt', null, array('label' => 'form.created_at'))
            ->add('name', null, array('label' => 'form.name'))
            ->add('family', null, array('label' => 'form.family'))
            ->add('phone', null, array('label' => 'form.phone'))
            ->add('email', null, array('label' => 'form.email'))
            ->add('commingFrom', null, array('label' => 'form.commingFrom'))
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
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
        $formMapper
            ->with('tab.general', array(
                'tab' => true,
            ))
                ->with('form.general', array(
                        'class' => 'col-md-12',
                        'label' => 'form.general',
                        'translation_domain' => 'NewVisionContactsBundle',
                    )
                )
                ->end()
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
                return 'NewVisionContactsBundle:Admin:show_action.html.twig';
                break;
            default:
                return parent::getTemplate($name);
                break;
        }
    }
}
