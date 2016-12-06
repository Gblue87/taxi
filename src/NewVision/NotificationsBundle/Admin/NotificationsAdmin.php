<?php

namespace NewVision\NotificationsBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;

class NotificationsAdmin extends Admin
{
    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'ASC',
        '_sort_by' => 'id',
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
            ->add('publishWorkflow.isActive', null, array('label' => 'form.isActive', 'editable' => true))
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
        $target = array('_self' => 'В същия прозорец', '_blank' => 'В нов прозорец');

        $formMapper
            ->with('tab.general', array(
                'tab' => true,
            ))
                ->with('form.general', array(
                        'class' => 'col-md-12',
                        'label' => 'form.general',
                        'translation_domain' => 'NewVisionNotificationsBundle',
                    )
                )
                    ->add('translations', 'a2lix_translations', array(
                        'fields' => array(
                            'title' => array(
                                'field_type' => 'text',
                                'label' => 'form.title',
                                'translation_domain' => 'NewVisionNotificationsBundle',
                            ),
                            'subTitle' => array(
                                'field_type' => 'text',
                                'label' => 'form.subTitle',
                                'translation_domain' => 'NewVisionNotificationsBundle',
                                'required' => false
                            ),
                            'buttonTitle' => array(
                                'field_type' => 'text',
                                'label' => 'form.buttonTitle',
                                'translation_domain' => 'NewVisionNotificationsBundle',
                            ),
                            'url' => array(
                                'field_type' => 'text',
                                'label' => 'form.url',
                                'translation_domain' => 'NewVisionNotificationsBundle',
                            ),
                            'target' => array(
                                'field_type' => 'choice',
                                'label' => 'form.target',
                                'choices' => $target,
                            ),
                        ),
                        'label' => 'form.translations',
                        'translation_domain' => 'NewVisionNotificationsBundle',
                    ))
                    ->add('time', 'integer', array(
                        'label' => 'form.time',
                        'required' => true,
                        'attr' => array(
                            'max' => 5000,
                            'min' => 500
                        )
                    ))
                ->end()
            ->end()
            ->with('Publish Workflow', array('tab' => true))
                ->with('Publish Workflow', array('class' => 'col-md-12', 'label' => 'form.general', 'translation_domain' => 'NewVisionNotificationsBundle'))
                    ->add('publishWorkflow', 'newvision_publish_workflow', array(
                        'is_active' => $this->getSubject()->getPublishWorkflow() ? $this->getSubject()->getPublishWorkflow()->getIsActive() : true,
                    ))
                ->end()
            ->end();
    }
}
