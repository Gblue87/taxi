<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\Sonata\UserBundle\Admin;

use Sonata\UserBundle\Admin\Model\UserAdmin as BaseUserAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sonata\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
/**
 *  Modified users's admin
 *
 * @package ApplicationSonataUserBundle
 * @author  Nikolay Tumbalev <n.tumbalev@newvision.bg>
 */
class UserAdmin extends BaseUserAdmin
{
    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('username')
            ->add('email')
            ->add('firstname', null, array('label' => 'table.firstname'))
            ->add('lastname', null, array('label' => 'table.lastname'))
            ->add('enabled', null, array('editable' => true))
            ->add('locked', null, array('editable' => true))
            ->add('createdAt')
            ->remove('batch')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'delete' => array(),

                ), 'label' => 'table.label_actions',
            ))
        ;

        /*if ($this->isGranted('ROLE_ALLOWED_TO_SWITCH')) {
            $listMapper
                ->add('impersonating', 'string', array('template' => 'SonataUserBundle:Admin:Field/impersonating.html.twig'))
            ;
        }*/
    }
    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        if($this->getRequest()->query->get('type')  == 'A'){
            return parent::configureFormFields($formMapper);
        }

        $formMapper
            ->with('tab.general', array('tab' => true))
                ->with('tab.general')
                    ->add('firstname')
                    ->add('lastname')
                    ->add('username')
                    ->add('email')
                    ->add('plainPassword', 'text', array(
                        'required' => (!$this->getSubject() || is_null($this->getSubject()->getId())),
                    ))
                ->end()
            ->end();

        if ($this->getSubject() && !$this->getSubject()->hasRole('ROLE_SUPER_ADMIN')) {
            $formMapper
            ->with('tab.management', array('tab' => true))
                ->with('tab.management',  array('translation_domain' => 'SonataUserBundle'))
                    ->add('groups', 'sonata_type_model', array(
                        'required' => false,
                        'expanded' => true,
                        'multiple' => true,
                    ))
                    ->add('realRoles', 'sonata_security_roles', array(
                        'label'    => 'form.label_roles',
                        'expanded' => true,
                        'multiple' => true,
                        'required' => false,
                        'translation_domain' => 'SonataUserBundle',

                    ))
                    ->add('locked', null, array('required' => false))
                    ->add('expired', null, array('required' => false))
                    ->add('enabled', null, array('required' => false))
                    ->add('credentialsExpired', null, array('required' => false))
                ->end()
            ->end()
            ;
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        #$collection->remove('export');
    }

    /**
     * {@inheritdoc}
     */
    public function preRemove($item)
    {
        $realRoles = $item->getRealRoles();
        foreach ($realRoles as $role) {
            if ($role == 'ROLE_SUPER_ADMIN') {
                throw new AccessDeniedException("Error processing request! You are not authorized to delete SUPER ADMINS");
            }
        }
    }

    /**
     * Hide the non-visible records from other than ROLE_SUPER_ADMIN
     * @param  string $context
     * @return object \Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery
     */
    public function createQuery($context = 'list')
    {
        $query = parent::createQuery($context);
        $sc =  $this->getConfigurationPool()->getContainer()->get('security.context');
        if (!$sc->isGranted('ROLE_SUPER_ADMIN')) {
            $query->andWhere(
                $query->expr()->notLike($query->getRootAlias().'.roles', ':role')
            );

            $query->setParameter('role', '%ROLE_SUPER_ADMIN%');
        }

        return $query;
    }

    public function preUpdate($item)
    {
        $sc = $this->getConfigurationPool()->getContainer()->get('security.context');
        if (!$sc->isGranted('ROLE_SUPER_ADMIN')) {
            if (in_array("ROLE_SUPER_ADMIN", $item->getRealRoles())) {
                throw new Exception('Permission Denied!');
            }
        }
        $this->getUserManager()->updateCanonicalFields($item);
        $this->getUserManager()->updatePassword($item);
    }

    /**
     * {@inheritdoc}
     */
    public function getPersistentParameters()
    {
        if (!$this->hasRequest()) {
            return array();
        }

        $type  = $this->getRequest()->get('type');

        return array(
            'type' => $type,
        );
    }

    /**
     * @param UserManagerInterface $userManager
     */
    public function setUserManager(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @return UserManagerInterface
     */
    public function getUserManager()
    {
        return $this->userManager;
    }
}
