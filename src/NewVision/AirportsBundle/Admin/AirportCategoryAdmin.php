<?php
/**
 * This file is part of the NewVisionAirportsBundle.
 *
 * (c) Nikolay Tumbalev <n.tumbalev@newvision.bg>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NewVision\AirportsBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;

/**
 *  Admin class for AirportCategory
 *
 * @package NewVisionAirportsBundle
 * @author Nikolay Tumbalev <n.tumbalev@newvision.bg>
 */
class AirportCategoryAdmin extends Admin
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

        $actions['hide'] = array(
            'label'            => $this->trans('action_hide', array(), 'NewVisionCoreBundle'),
            'ask_confirmation' => true, // If true, a confirmation will be asked before performing the action
        );
        $actions['show'] = array(
            'label'            => $this->trans('action_show', array(), 'NewVisionCoreBundle'),
            'ask_confirmation' => true, // If true, a confirmation will be asked before performing the action
        );

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
        } else if ($action == 'tree' || $action == 'list' || $action == 'trash') {
            $menu->addChild(
                $this->getTranslator()->trans("action.list", array(), 'NewVisionCoreBundle'),
                array('uri' => $this->generateUrl('list'))
            );
            $menu->addChild(
                $this->getTranslator()->trans("action.tree", array(), 'NewVisionCoreBundle'),
                array('uri' => $this->generateUrl('tree'))
            );
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
                ->add('createdAt')
                ->add('updatedAt')
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
        $collection->add('tree', 'tree');
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
            ->add('lft', 'string', array('template' => 'NewVisionCoreBundle:Admin:list_parent.html.twig', 'label' => 'form.parent'))
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
        $translationAdmin = $this->configurationPool->getAdminByAdminCode('newvision.airports.admin.airport_category_translations');
        $ffds = $translationAdmin->getFormFieldDescriptions();
        $ffds['image']->setAssociationAdmin($mediaAdmin);

        $object = $this->getSubject();

        $em = $this->getConfigurationPool()->getContainer()->get('doctrine')->getManager();
        $repo = $em->getRepository($this->getClass());
        $query = $repo->createQueryBuilder('c')
            ->select('c.id')
            ->where('c.lft > :lft and c.rgt < :rgt and c.root = :root')
            ->setParameters(array(
                'lft' => $object->getLft(),
                'rgt' => $object->getRgt(),
                'root'=> $object->getRoot()
            ))
            ->getQuery();
        $allChildrenIds = $query->getArrayResult();
        $disabled_ids = array_map(function($obj) {
            return $obj['id'];
        }, $allChildrenIds);

        $formMapper
            ->with('tab.general', array('tab' => true))
                ->add('parent', null, array('required' => false, 'label' => 'form.parent',
                    'class' => 'NewVision\AirportsBundle\Entity\AirportCategory',
                    'orderFields' => array('root', 'lft'),
                    'treeLevelField' => 'lvl',
                    'add_empty' => $this->trans('select.parent'),
                    'disabled_ids' => $disabled_ids,
                    'max_level' => 1
                ))
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
                            'required' => false
                        ),
                        'description' => array(
                            'field_type' => 'textarea',
                            'label' => 'form.description',
                            'required' => false,
                            'attr' => array(
                                'data-theme' => 'bbcode',
                                'class' => 'tinymce'
                            )
                        ),
                        'image' => array(
                            'label' => 'form.image',
                            'required' => false,
                            'field_type' => 'sonata_type_model_list',
                            'model_manager' => $this->getModelManager(),
                            'sonata_field_description' => $ffds['image'],
                            'class' => $mediaAdmin->getClass(),
                            'sonata_admin' => $mediaAdmin->getClass(),
                            'translation_domain' => 'NewVisionAirportsBundle',
                        ),
                    ),
                    'translation_domain' => 'NewVisionAirportsBundle',
                    'label' => 'form.translations',
                ))
                ->end()
            ->end()
            ->with('SEO', array('tab' => true))
                ->with('SEO', array('collapsed' => true, 'class' => 'col-md-12'))
                    ->add('metaData', 'meta_data')
                ->end()
            ->end()
            ->with('tab.publish_workflow', array('tab' => true))
                ->with('Publish Workflow', array('class' => 'col-md-12', 'label' => 'form.general', 'translation_domain' => 'NewVisionAirportsBundle'))
                    ->add('publishWorkflow', 'newvision_publish_workflow', array(
                        'is_active' => $this->getSubject()->getPublishWorkflow() ? $this->getSubject()->getPublishWorkflow()->getIsActive() : true,
                    ))
                ->end()
            ->end();
    }
}
