<?php
namespace NewVision\CoreBundle\Route;

use Sonata\AdminBundle\Model\AuditManagerInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Builder\RouteBuilderInterface;
use Sonata\AdminBundle\Route\PathInfoBuilder;
use Sonata\AdminBundle\Route\RouteCollection;

class EntityRouterBuilder extends PathInfoBuilder implements RouteBuilderInterface
{

    protected $manager;

    /**
     * @param \Sonata\AdminBundle\Model\AuditManagerInterface $manager
     */
    public function __construct(AuditManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param \Sonata\AdminBundle\Admin\AdminInterface $admin
     * @param \Sonata\AdminBundle\Route\RouteCollection $collection
     */
    public function build(AdminInterface $admin, RouteCollection $collection)
    {
        parent::build($admin, $collection);
        $collection->add('trash', 'trash');
        $collection->add('untrash', $admin->getRouterIdParameter() . '/untrash');

        $collection->add('history', $admin->getRouterIdParameter().'/history');
        $collection->add('history_view_revision', $admin->getRouterIdParameter().'/preview/{revision}');
        $collection->add('history_revert_to_revision', $admin->getRouterIdParameter().'/revert/{revision}');
    }
}