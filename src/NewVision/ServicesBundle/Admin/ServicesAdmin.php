<?php
/**
 * This file is part of the NewVisionServicesBundle.
 *
 * (c) Nikolay Tumbalev <n.tumbalev@newvision.bg>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NewVision\ServicesBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;

/**
 *  Admin class for Services
 *
 * @package NewVisionServicesBundle
 * @author Nikolay Tumbalev <n.tumbalev@newvision.bg>
 */
class ServicesAdmin extends Admin
{
    /**
     * @inheritdoc
     */
    protected $datagridValues = array(
         '_page' => 1,
         '_sort_order' => 'ASC',
         '_sort_by' => 'rank',
     );

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->with('General')
                ->add('title')
                ->add('created_at')
                ->add('updated_at')
            ->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        $collection->add('history', $this->getRouterIdParameter().'/history');
        $collection->add('history_view_revision', $this->getRouterIdParameter().'/preview/{revision}');
        $collection->add('history_revert_to_revision', $this->getRouterIdParameter().'/revert/{revision}');
        $collection->add('order', 'order');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title', null, array('label' => 'form.title'))
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('title', null, array('label' => 'list.title'))
            ->add('publishWorkflow.isActive', null, array('label' => 'list.isActive', 'editable' => true))
            ->add('_action', 'actions', array(
                    'actions' => array(
                        'edit' => array(),
                        'delete' => array(),
                        'history' => array('template' => 'NewVisionCoreBundle:Admin:list_action_history.html.twig'),
                    ), 'label' => 'actions',
                ))
            ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        $mediaAdmin = $this->configurationPool->getAdminByClass("Application\\Sonata\\MediaBundle\\Entity\\Media");
        $galleryAdmin = $this->configurationPool->getAdminByClass("Application\\Sonata\\MediaBundle\\Entity\\Gallery");
        $translationAdmin = $this->configurationPool->getAdminByAdminCode('newvision.services.admin.services_translations');
        $ffds = $translationAdmin->getFormFieldDescriptions();
        $ffds['image']->setAssociationAdmin($mediaAdmin);
        $ffds['gallery']->setAssociationAdmin($galleryAdmin);

        $formMapper
            ->with('tab.general', array('tab' => true))
                // ->add('serviceCategories', 'sonata_type_model', array(
                //     'expanded' => false,
                //     'multiple' => true,
                //     'btn_add' => false,
                //     'label' => 'form.categories'
                // ))
                // ->add('referenceNo', null, array(
                //     'label' => 'form.referenceNo',
                //     'required' => false
                // ))
                //->add('shareIcons', null, array('label' => 'form.showSocialIcons'))
                ->add('translations', 'a2lix_translations', array(
                    'fields' => array(
                        'slug' => array(
                            'field_type' => 'text',
                            'label' => 'form.slug',
                            'required' => false
                        ),
                        'title' => array(
                            'field_type' => 'text',
                            'label' => 'form.title'
                        ),
                        'simpleDescription' => array(
                            'field_type' => 'textarea',
                            'label' => 'form.simpleDescription',
                            'required' => false,
                        ),
                        'description' => array(
                            'field_type' => 'textarea',
                            'label' => 'form.description',
                            'required' => false,
                            'attr' => array(
                                'class' => 'tinymce',
                                'data-theme' => 'bbcode'
                            )
                        ),
                        // 'tabDescription' => array(
                        //     'field_type' => 'textarea',
                        //     'label' => 'form.tabDescription',
                        //     'required' => false,
                        //     'attr' => array(
                        //         'class' => 'tinymce',
                        //         'data-theme' => 'bbcode'
                        //     )
                        // ),
                        // 'tabTech' => array(
                        //     'field_type' => 'textarea',
                        //     'label' => 'form.tabTech',
                        //     'required' => false,
                        //     'attr' => array(
                        //         'class' => 'tinymce',
                        //         'data-theme' => 'bbcode'
                        //     )
                        // ),
                        'image' => array(
                            'label' => 'Изображение (Препоръчителен минимален размер 282px x 211px)',
                            'required' => false,
                            'field_type' => 'sonata_type_model_list',
                            'model_manager' => $this->getModelManager(),
                            'sonata_field_description' => $ffds['image'],
                            'class' => $mediaAdmin->getClass(),
                            'sonata_admin' => $mediaAdmin->getClass(),
                            'translation_domain' => 'NewVisionServicesBundle',
                        ),
                        // 'gallery' => array(
                        //     'label' => 'form.gallery',
                        //     'required' => false,
                        //     'field_type' => 'sonata_type_model_list',
                        //     'model_manager' => $this->getModelManager(),
                        //     'sonata_field_description' => $ffds['gallery'],
                        //     'class' => $galleryAdmin->getClass(),
                        //     'translation_domain' => 'NewVisionNewsBundle',
                        // ),
                        'price' => array(
                            'field_type' => 'text',
                            'label' => 'Цена до 4 човека',
                            'required' => false
                        ),
                        'middlePrice' => array(
                            'field_type' => 'text',
                            'label' => 'Цена от 4 до 6 човека',
                            'required' => false
                        ),
                        'doublePrice' => array(
                            'field_type' => 'text',
                            'label' => 'Цена от 6 до 8 човека',
                            'required' => false
                        ),
                        // 'youTubeVideo' => array(
                        //     'field_type' => 'text',
                        //     'label' => 'form.youTubeVideo',
                        //     'required' => false
                        // ),
                        // 'buttonTitle' => array(
                        //     'field_type' => 'text',
                        //     'label' => 'form.button_title',
                        //     'translation_domain' => 'NewVisionServicesBundle',
                        //     'required' => false
                        // ),
                    ),
                    'translation_domain' => 'NewVisionServicesBundle',
                    'label' => 'form.translations',
                    'exclude_fields' => array('buttonTitle', 'youTubeVideo', 'gallery', 'tabTech', 'tabDescription', 'from', 'to')
                ))
                //->add('from', null, array('label' => 'form.from'))
                ->add('to', null, array('label' => 'form.destination'))
                ->add('isHomepage', null, array('label' => 'form.isHomepage'))
                ->end()
            ->end()
            ->with('SEO', array('tab' => true))
                ->with('SEO', array('collapsed' => true, 'class' => 'col-md-12'))
                    ->add('metaData', 'meta_data')
                ->end()
            ->end()
            ->with('tab.publish_workflow', array('tab' => true))
                ->with('Publish Workflow', array('class' => 'col-md-12', 'label' => 'form.general', 'translation_domain' => 'NewVisionServicesBundle'))
                    ->add('publishWorkflow', 'newvision_publish_workflow', array(
                        'is_active' => $this->getSubject()->getPublishWorkflow() ? $this->getSubject()->getPublishWorkflow()->getIsActive() : true,
                    ))
                ->end()
            ->end();
    }
}
