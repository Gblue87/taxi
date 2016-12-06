<?php
namespace NewVision\MenuBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;

class MenuAdmin extends Admin
{
    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'ASC',
        '_sort_by' => 'lft',
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
        $trans = $this->getTranslator();
        if ($action == 'history') {
            $id = $this->getRequest()->get('id');
            $menu->addChild(
                $trans->trans("menu.item", array(), 'NewVisionMenuBundle'),
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

    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);

        $collection->add('tree', 'tree');
        $collection->add('order', 'order');

        $collection->remove('export');
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title', null, array('label' => 'form.title'))
            ->add('url', null, array('label' => 'form.url'))
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
            ->add('lft', 'string', array('template' => 'NewVisionCoreBundle:Admin:list_parent.html.twig', 'label' => 'form.path'))
            ->add('_action', 'actions', array(
                    'actions' => array(
                        'edit' => array(),
                        'delete' => array(),
                        'history' => array('template' => 'NewVisionCoreBundle:Admin:list_action_history.html.twig'),
                    ),
                    'label' => 'form.label_actions',
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
        $trans = $this->getTranslator();
        $choices = array('_self' => $trans->trans('self', array(), 'NewVisionMenuBundle'), '_blank' =>  $trans->trans('blank', array(), 'NewVisionMenuBundle'));
        $object = $this->getSubject();

        $em = $this->getConfigurationPool()->getContainer()->get('doctrine')->getManager();
        $repo = $em->getRepository($this->getClass());
        $query = $repo->createQueryBuilder('c')
            ->select('c.id')
            ->where('c.lft > :lft and c.rgt < :rgt and c.root = :root')
            ->setParameters(array(
                'lft' => $object->getLft(),
                'rgt' => $object->getRgt(),
                'root' => $object->getRoot(),
            ))
            ->getQuery();
        $allChildrenIds = $query->getArrayResult();
        $disabled_ids = array_map(function ($obj) {
            return $obj['id'];
        }, $allChildrenIds);

        $formMapper
            ->with('form.general', array('tab' => true, 'label' => 'form.general'))
                ->with('form.general', array('label' => 'form.general', 'class' => 'col-md-12', 'style' => 'margin: 0 20px;'))
                    ->add('parent',  'newvision_tree', array(
                        'required' => false,
                        'label' => 'form.parent',
                        'class' => $this->getClass(),
                        'orderFields' => array('root', 'lft'),
                        'treeLevelField' => 'lvl',
                        'add_empty' => $this->trans('select.parent'),
                        'disabled_ids' => $disabled_ids,
                    ))
                    ->add('translations', 'a2lix_translations', array(
                        'fields' => array(
                            'title' => array(
                                'field_type' => 'text',
                                'label' => 'form.title',
                            ),
                            'url' => array(
                                'field_type' => 'text',
                                'label' => 'form.url',
                            ),
                            'target' => array(
                                'field_type' => 'choice',
                                'label' => 'form.target',
                                'choices' => $choices,
                            ),
                        ),
                        'label' => 'form.translations',
                        'translation_domain' => 'NewVisionMenuBundle',
                    ))
                ->end()
            ->end()
            ->with('form.more', array('tab' => true, 'label' => 'form.more'))
                ->with('form.more', array('label' => 'form.more', 'class' => 'col-md-12', 'style' => 'margin: 0 20px;'))
                    ->add('icon', 'text', array('label' => 'form.icon', 'required' => false))
                    ->add('class', 'text', array('label' => 'form.class', 'required' => false))
                    ->add('image', 'sonata_type_model_list', array('required' => false, 'label' => 'form.label_image'), array(
                        'link_parameters' => array(
                            'context' => 'newvision_menu',
                        )
                    ))
                ->end()
            ->end()
            ->with('Publish Workflow', array('tab' => true))
                ->with('Publish Workflow', array('collapsed' => true, 'class' => 'col-md-12'))
                    ->add('publishWorkflow', 'newvision_publish_workflow', array(
                        'is_active' => $this->getSubject()->getPublishWorkflow() ? $this->getSubject()->getPublishWorkflow()->getIsActive() : true,
                    ))
                ->end()
            ->end();
    }
}
