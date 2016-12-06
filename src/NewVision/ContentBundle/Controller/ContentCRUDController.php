<?php

namespace NewVision\ContentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use NewVision\CoreBundle\Controller\TreeCRUDController;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Sonata\AdminBundle\Exception\ModelManagerException;

/**
 * TreeCRUDController extends HistoryCURDController
 * and implements tree functionality
 *
 * @author Hristo Hristoff <hristo.hristov@newvision.bg>
 */
class ContentCRUDController extends TreeCRUDController
{

	/**
     * Execute a batch delete
     *
     * @param ProxyQueryInterface $query
     *
     * @return RedirectResponse
     *
     * @throws AccessDeniedException If access is not granted
     */
    public function batchActionDelete(ProxyQueryInterface $query)
    {
        if (false === $this->admin->isGranted('DELETE')) {
            throw new AccessDeniedException();
        }

        $modelManager = $this->admin->getModelManager();
        try {
        	$entityManager = $this->admin->getConfigurationPool()->getContainer()->get('doctrine')->getManager();

            $i = 0;
            foreach ($query->getQuery()->iterate() as $pos => $object) {
        		$this->removeItemAndAllChildren($entityManager, $object[0]);

                if ((++$i % 20) == 0) {
                    $entityManager->flush();
                    $entityManager->clear();
                }
            }
            $entityManager->flush();
            $entityManager->clear();

            $this->addFlash('sonata_flash_success', 'flash_batch_delete_success');
        } catch (ModelManagerException $e) {
            $this->addFlash('sonata_flash_error', 'flash_batch_delete_error');
        }

        return new RedirectResponse($this->admin->generateUrl(
            'list',
            array('filter' => $this->admin->getFilterParameters())
        ));
    }

    private function removeItemAndAllChildren($entityManager, $item) {
    	$children = $item->getChildren();
    	foreach ($children as $child) {
    		$this->removeItemAndAllChildren($entityManager, $child);
    	}
    	if($this->admin->isGranted('DELETE', $item)) {
			$entityManager->remove($item);
		} else {
			throw new ModelManagerException("Child required newvision admin permissions");
		}
    }
}