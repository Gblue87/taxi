<?php

namespace NewVision\BannersBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;

class BannersAdmin extends Admin
{
    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'ASC',
        '_sort_by' => 'rank',
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
        $collection->add('history', $this->getRouterIdParameter().'/history');
        $collection->add('history_view_revision', $this->getRouterIdParameter().'/preview/{revision}');
        $collection->add('history_revert_to_revision', $this->getRouterIdParameter().'/revert/{revision}');
        $collection->add('order', 'order');
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
        $types = array('big' => 'spacer', 'small' => '1/3', 'medium' => '2/3');
        $labelTypes = array('label-type-1' => 'Purple', 'label-type-2' => 'Gold', 'label-type-3' => 'Red');

        $mediaAdmin = $this->configurationPool->getAdminByClass("Application\\Sonata\\MediaBundle\\Entity\\Media");
        $translationAdmin = $this->configurationPool->getAdminByAdminCode('newvision.banners.admin.banners_translation');
        $ffds = $translationAdmin->getFormFieldDescriptions();
        $ffds['image']->setAssociationAdmin($mediaAdmin);

        $formMapper
            ->with('tab.general', array(
                'tab' => true,
            ))
                ->with('form.general', array(
                        'class' => 'col-md-12',
                        'label' => 'form.general',
                        'translation_domain' => 'NewVisionBannersBundle',
                    )
                )
                    ->add('translations', 'a2lix_translations', array(
                        'fields' => array(
                            'type' => array(
                                'field_type' => 'choice',
                                'label' => 'form.type',
                                'choices' => $types,
                            ),
                            'title' => array(
                                'field_type' => 'text',
                                'label' => 'form.title',
                                'translation_domain' => 'NewVisionBannersBundle',
                            ),
                            'subTitle' => array(
                                'field_type' => 'text',
                                'label' => 'form.subTitle',
                                'translation_domain' => 'NewVisionBannersBundle',
                                'required' => false,
                            ),
                            'buttonTitle' => array(
                                'field_type' => 'text',
                                'label' => 'form.button_title',
                                'translation_domain' => 'NewVisionBannersBundle',
                                'required' => false
                            ),
                            'labelType' => array(
                                'field_type' => 'choice',
                                'choices' => $labelTypes,
                                'label' => 'form.labelType',
                                'translation_domain' => 'NewVisionBannersBundle',
                                'required' => true,
                            ),
                            'labelTitle' => array(
                                'field_type' => 'text',
                                'label' => 'form.labelTitle',
                                'translation_domain' => 'NewVisionBannersBundle',
                                'required' => false,
                            ),
                            'image' => array(
                                'label' => 'Изображение',
                                'required' => true,
                                'field_type' => 'sonata_type_model_list',
                                'model_manager' => $this->getModelManager(),
                                'sonata_field_description' => $ffds['image'],
                                'class' => $mediaAdmin->getClass(),
                                'translation_domain' => 'NewVisionBannersBundle',
                            ),
                            'url' => array(
                                'field_type' => 'text',
                                'label' => 'form.url',
                                'translation_domain' => 'NewVisionBannersBundle',
                                'required' => false,
                            ),
                            // 'target' => array(
                            //     'field_type' => 'choice',
                            //     'label' => 'form.target',
                            //     'choices' => $target,
                            // ),
                        ),
                        'exclude_fields' => array('target'),
                        'label' => 'form.translations',
                        'translation_domain' => 'NewVisionBannersBundle',
                    ))
                ->end()
            ->end()
            ->with('Publish Workflow', array('tab' => true))
                ->with('Publish Workflow', array('class' => 'col-md-12', 'label' => 'form.general', 'translation_domain' => 'NewVisionBannersBundle'))
                    ->add('publishWorkflow', 'newvision_publish_workflow', array(
                        'is_active' => $this->getSubject()->getPublishWorkflow() ? $this->getSubject()->getPublishWorkflow()->getIsActive() : true,
                    ))
                ->end()
            ->end();
    }
}
