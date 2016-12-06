<?php
namespace NewVision\TranslationsBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;

class TranslationsAdmin extends Admin
{
    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        $collection->add('clear-cache');
        $collection->add('history', $this->getRouterIdParameter().'/history');
        $collection->add('history_view_revision', $this->getRouterIdParameter().'/preview/{revision}');
        $collection->add('history_revert_to_revision', $this->getRouterIdParameter().'/revert/{revision}');
        $collection->remove('delete');
        $collection->remove('export');
    }

    protected function configureTabMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        parent::configureTabMenu($menu, $action, $childAdmin);

        $menu->addChild(
            $this->getTranslator()->trans("clear-cache", array(), 'NewVisionTranslationsBundle'),
            array('uri' => $this->generateUrl('clear-cache'))
        );
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('file', null, array('label' => 'form.file'))
            ->add('trans_unit.key', null, array('label' => 'form.trans_unit'))
            ->add('locale', null, array('label' => 'form.locale'))
            ->add('content', null, array('label' => 'form.content'))
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {

        $listMapper
            ->add('file', null, array('label' => 'list.file'))
            ->add('locale', null, array('label' => 'list.locale'))
            ->add('trans_unit', null, array('label' => 'list.trans_unit'))
            ->add('content', null, array('editable' => true, 'label' => 'list.content'))
            ->add('_action', 'actions', array(
                    'actions' => array(
                        'history' => array('template' => 'NewVisionCoreBundle:Admin:list_action_history.html.twig'),
                    ),
                    'label' => 'form.actions',
                ))
            ;
        ;
    }

    /**
     * Configure the form
     *
     * @param FormMapper $formMapper formMapper
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        $locales = $this->getConfigurationPool()->getContainer()->getParameter('newvision_translations.managed_locales');
        $locales = array_combine($locales, $locales);

        $formMapper
            ->with('General')
                ->add('file', null, array('label' => 'form.file'))
                ->add('locale', 'choice', array('choices' => $locales, 'label' => 'form.locale'))
                ->add('trans_unit', 'sonata_type_model_list', array('label' => 'form.trans_unit'))
                ->add('content', null, array('label' => 'form.content'))
            ->end();
    }
}