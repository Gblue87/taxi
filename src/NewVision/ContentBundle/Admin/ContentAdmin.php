<?php
namespace NewVision\ContentBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;

class ContentAdmin extends Admin
{
    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'ASC',
        '_sort_by' => 'lft',
    );

    /**
     * {@inheritdoc}
     */
    public function isGranted($name, $object = null)
    {
        $res = parent::isGranted($name, $object);
        $entity = $this->getConfigurationPool()->getContainer()->getParameter('newvision.content.admin.content.entity');

        if($object instanceof $entity) {
            $newvisionAdmin = $this->getConfigurationPool()->getContainer()->get('security.context')->isGranted('ROLE_SUPER_ADMIN');
            if($name == 'DELETE' && $object->getIsSystem() && !$newvisionAdmin ) {
                return false;
            }
        }

        return $res;
    }

    protected function configureTabMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        parent::configureTabMenu($menu, $action, $childAdmin);
        if($action == 'history') {
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
        } else if ($action == 'tree' || $action == 'list') {
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

    public function getBatchActions()
    {
        $actions = parent::getBatchActions();

        $actions['hide'] = [
            'label'            => $this->trans('action_hide', array(), 'NewVisionCoreBundle'),
            'ask_confirmation' => true // If true, a confirmation will be asked before performing the action
        ];
        $actions['show'] = [
            'label'            => $this->trans('action_show', array(), 'NewVisionCoreBundle'),
            'ask_confirmation' => true // If true, a confirmation will be asked before performing the action
        ];

        return $actions;
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->with('General')
                ->add('slug')
                ->add('title')
                ->add('description')
                ->add('createdAt')
                ->add('updatedAt')
            ->end()
        ;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        $collection->add('tree', 'tree');
        $collection->add('order', 'order');
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title', null, array('label' => 'form.title'))
            ->add('description', null, array('label' => 'form.description'))
        ;
    }

    /**
     * Configure the list
     *
     * @param \Sonata\AdminBundle\Datagrid\ListMapper $list list
     */
    protected function configureListFields(ListMapper $list)
    {
        $request = $this->getRequest();
        $list
            ->addIdentifier('title', null, array('label' => 'form.title'))
            ->add('lft', 'string', array('template' => 'NewVisionCoreBundle:Admin:list_parent.html.twig', 'label' => 'form.lft'))
            ->add('publishWorkflow.isActive', null, array('label' => 'form.isActive', 'editable' => true))
            ->add('createdAt', null, array('label' => 'form.createdAt'))
            ->add('_action', 'actions', array(
                    'actions' => array(
                        'edit' => array(),
                        'delete' => array(),
                        'history' => array('template' => 'NewVisionCoreBundle:Admin:list_action_history.html.twig'),
                    ),'label' => 'actions'
                ))
            ;
    }

    /**
     * Configure the form
     *
     * @param FormMapper $formMapper formMapper
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        $mediaAdmin = $this->configurationPool->getAdminByClass("Application\\Sonata\\MediaBundle\\Entity\\Media");
        $translationAdmin = $this->configurationPool->getAdminByAdminCode('newvision.content.admin.content_translations');
        $ffds = $translationAdmin->getFormFieldDescriptions();
        $ffds['headerImage']->setAssociationAdmin($mediaAdmin);

        $superAdmin = $this->getConfigurationPool()->getContainer()->get('security.context')->isGranted('ROLE_SUPER_ADMIN');
        $object = $this->getSubject();

        $em = $this->getConfigurationPool()->getContainer()->get('doctrine')->getManager();
        $repo = $em->getRepository($this->getClass());
        $query = $repo->createQueryBuilder('c')
            ->select('c.id')
            ->where('c.lft > :lft and c.rgt < :rgt and c.root = :root')
            ->setParameters(array(
                'lft' => $object->getLft(),
                'rgt' => $object->getRgt(),
                'root'=> $object->getRoot(),
            ))
            ->getQuery();
        $allChildrenIds = $query->getArrayResult();
        $disabled_ids = array_map(function($obj) {
            return $obj['id'];
        }, $allChildrenIds);

        $a2lixFields = array('fields' => array(
                    'slug' => array(
                        'field_type' => 'text',
                        'label' => 'form.slug',
                        'required' => false,
                    ),
                    'title' => array(
                        'field_type' => 'text',
                        'label' => 'form.title'
                    ),
                    'description' => array(
                        'field_type' => 'textarea',
                        'label' => 'form.description',
                        'required' => false,
                        'attr' => array(
                            'class' => 'tinymce',
                            'data-theme' => 'bbcode'
                        )
                    )
                ),
                'translation_domain' => 'NewVisionContentBundle',
                'label' => 'form.translations',
                'exclude_fields' => array('headerImage'),
        );
        if (!$object->isNew() && $object->getIsSystem() && !$superAdmin) {
            $a2lixFields['fields']['slug']['display'] = false;
        }

        $formMapper
        ->with('General', array('tab' => true))
            ->with('General', array('collapsed' => true, 'class' => 'col-md-12'))
                ->add('parent', 'newvision_tree', array('required' => false, 'label' => 'form.parent',
                    'class' => 'NewVision\ContentBundle\Entity\Content',
                    'orderFields' => array('root', 'lft'),
                    'treeLevelField' => 'lvl',
                    'add_empty' => $this->trans('select.parent'),
                    'disabled_ids' => $disabled_ids,
                    'max_level' => 0
                ))
                ->add('translations', 'a2lix_translations', $a2lixFields)
                //->add('shareIcons', null, array('label' => 'form.showSocialIcons'))
            ->end()
        ->end()
        ->with('SEO', array('tab' => true))
            ->with('SEO', array('collapsed' => true, 'class' => 'col-md-12'))
                ->add('metaData', 'meta_data')
            ->end()
        ->end();
        if ($object->isNew() || (!$object->isNew() && !$object->getIsSystem())  || $superAdmin) {
            $formMapper->with('Publish Workflow', array('tab' => true))
                ->with('Publish Workflow', array('collapsed' => true, 'class' => 'col-md-12'))
                    ->add('publishWorkflow', 'newvision_publish_workflow', array(
                        'is_active' => $this->getSubject()->getPublishWorkflow() ? $this->getSubject()->getPublishWorkflow()->getIsActive() : true)
                    )
                ;
            if ($superAdmin)
                $formMapper->add('isSystem', 'checkbox', array('required'=>false, 'label' => 'form.isSystem'));
            $formMapper->end();
            $formMapper->end();
        }

    }
}
