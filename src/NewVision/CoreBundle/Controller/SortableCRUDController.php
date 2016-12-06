<?php

namespace NewVision\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;


/**
 *
 */
class SortableCRUDController extends HistoryCRUDController
{

    /**
     * List action
     *
     * @return Response
     *
     * @throws AccessDeniedException If access is not granted
     */
    public function listAction(Request $request = null)
    {
        $this->admin->setTemplate('list', 'NewVisionCoreBundle:Admin:sortable_list.html.twig');
        return parent::listAction();
    }

    public function orderAction(Request $request)
    {
        if ($request->isMethod('POST')) {
            $translator = $this->get('translator');
            $ids = $request->request->get('item', array());
            $page = (int)$request->request->get('page');
            $perPage = (int)$request->request->get('perPage');

            $em = $this->admin->getConfigurationPool()->getContainer()->get('doctrine')->getManager();
            $repo = $em->getRepository($this->admin->getClass());

            $i = ($page * $perPage) - $perPage;

            foreach ($ids as $id) {
                $i += 1;
                $item = $repo->findOneById((int)$id);
                $item->setRank($i);
                $em->persist($item);
                $em->flush();
            }

            if ($i !== 0)
                return new JsonResponse(array('success' => true, 'html' => $translator->trans('order_success', array(), 'NewVisionCoreBundle')));
        }
        return new JsonResponse(array('success' => false, 'html' => $translator->trans('order_error', array(), 'NewVisionCoreBundle')));
    }
}