<?php

namespace NewVision\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;


/**
 * TreeCRUDController extends HistoryCURDController
 * and implements tree functionality
 *
 * @author Hristo Hristoff <hristo.hristov@newvision.bg>
 */
class TreeCRUDController extends HistoryCRUDController
{
    /**
     * Show current tree
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function treeAction($maxLvl = 10)
    {
        $this->admin->buildTabMenu('tree');

        $em = $this->admin->getConfigurationPool()->getContainer()->get('doctrine')->getManager();
        $repo = $em->getRepository($this->admin->getClass());


        $query = $em
            ->createQueryBuilder()
            ->select('node')
            ->from($this->admin->getClass(), 'node')
            ->orderBy('node.root, node.lft', 'ASC')
            ->getQuery()
        ;
        $options = array(
            'decorate' => true,
            'rootOpen' => '<ol class="page-tree">',
            'rootClose' => '</ol>',
            'childOpen' => function($node) { return '<li id="list_'.$node['id'].'">'; },
            'childClose' => '</li>',
            'nodeDecorator' => function($node) {
                $em = $this->admin->getConfigurationPool()->getContainer()->get('doctrine')->getManager();
                $repo = $em->getRepository($this->admin->getClass());

                $a = $repo->findOneBy(array('id' => $node['id']));
                $id = $node['id'];
                $url = $this->admin->generateUrl('edit', array('id' => $node['id']));
                $title = $a->getTitle();
                $str = <<<CODE
                <div id="{$id}" class="page-tree__item">
                    <i class="fa fa-caret-right"></i>
                    <a class="page-tree__item__edit" href="{$url}">{$title}</a>
                </div>
CODE;
                return $str;
            }
        );
        $htmlTree = $repo->buildTree($query->getArrayResult(), $options);

        $datagrid = $this->admin->getDatagrid();
        $formView = $datagrid->getForm()->createView();

        $this->get('twig')->getExtension('form')->renderer->setTheme($formView, $this->admin->getFilterTheme());

        return $this->render('NewVisionCoreBundle:Admin:tree.html.twig', array(
            'action'      => 'tree',
            'tree'       => $htmlTree,
            'form'        => $formView,
            'csrf_token'  => $this->getCsrfToken('sonata.batch'),
            'maxLvl' => (int)$maxLvl,
        ));
    }

    /**
     * Reorder tree
     *
     * @param  Request $request
     * @return JsonResponse Return JSON object with success property. If success is true tree ordering is successfully.
     */
    public function orderAction(Request $request)
    {
        if ($request->isMethod('POST')) {
            $list = $request->request->get('list');

            $em = $this->admin->getConfigurationPool()->getContainer()->get('doctrine')->getManager();
            $conn = $em->getConnection();
            $meta = $em->getClassMetadata($this->admin->getClass());
            $tableName = $meta->getTableName();

            $this->updateTree($conn, $tableName, $list);

            return new JsonResponse(array('success' => true));
        }
        return new JsonResponse(array('success' => false));
    }

    /**
     * Update tree ordering
     *
     * @param  $em   Doctrine Entity Manager
     * @param  $repo Current tree repository
     * @param  $list array with new tree structure
     * @param  $root root node
     */
    private function updateTree($conn, $tableName, $list) {
        foreach ($list as $itm) {
            $updateSQL = '
            UPDATE '.$tableName.'
            SET parent_id=:parent_id,
            lft=:lft,
            rgt=:rgt,
            lvl=:lvl,
            root=:root
            WHERE id=:id
        ';
        $st = $conn->prepare($updateSQL);
        foreach ($list as $itm) {
            if($itm['item_id'] == 'root') continue;
            $root = 1;

            $st->execute(array(
                'parent_id' => ($itm['parent_id'] != 'none' && $itm['parent_id'] != 'root') ? (int)$itm['parent_id'] : null,
                'lft' => (int)$itm['left'] - 1,
                'rgt' => (int)$itm['right'] - 1,
                'lvl' => (int)$itm['depth'] - 1,
                'root' => (int)$root,
                'id' => (int)$itm['item_id']
            ));
        }
        }
    }
}