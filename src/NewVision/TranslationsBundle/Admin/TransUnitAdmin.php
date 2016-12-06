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

class TransUnitAdmin extends Admin
{
    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('domain', null, array('label' => 'form.domain'))
            ->add('key', null, array('label' => 'form.key'))
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {

        $listMapper
            ->add('domain', null, array('label' => 'list.domain'))
            ->add('key', null, array('label' => 'list.key'))
        ;
    }

    /**
     * Configure the form
     *
     * @param FormMapper $formMapper formMapper
     */
    public function configureFormFields(FormMapper $formMapper)
    {

        $em = $this->getConfigurationPool()->getContainer()->get('doctrine')->getManager();

        $domainsResult = $em->getRepository('NewVisionTranslationsBundle:TransUnit')->createQueryBuilder('t')
                        ->orderBy('t.domain', 'ASC')
                        ->groupBy('t.domain')
                        ->getQuery()->getResult();
        $domains = array();
        foreach ($domainsResult as $obj) {
            $domains[$obj->getId()] = $obj->getDomain();
        }
        $formMapper
            ->with('General')
                ->add('domain', 'choice', array('choices' => $domains, 'label' => 'form.domain'))
                ->add('key', null, array('label' => 'form.key'))
            ->end();
    }
}