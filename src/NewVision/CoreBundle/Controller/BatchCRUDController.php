<?php

namespace NewVision\CoreBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sonata\AdminBundle\Admin\FieldDescriptionCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;

/**
 *
 */
class BatchCRUDController extends CRUDController
{
    public function batchActionHide(ProxyQuery $selectedModelQuery)
    {
        if ($this->admin->isGranted('EDIT') === false)
        {
            throw new AccessDeniedException();
        }

        $request = $this->get('request');
        $entityManager = $this->admin->getConfigurationPool()->getContainer()->get('doctrine')->getManager();
        $all = $request->request->get('all_elements');
        // do the merge work here
        try {
            $repo = $entityManager->getRepository($this->admin->getClass());
            if(!$all) {
                $q = $repo->createQueryBuilder('t')
                    ->where('t.id IN(:idx)')
                    ->setParameters(array(':idx' => $request->request->get('idx')))
                    ->getQuery();
                $items = $q->getResult();
            } else {
                $q = $repo->createQueryBuilder('t')
                    ->getQuery();
                $items = $q->getResult();
            }
            foreach ($items as $item) {
                $pw = $item->getPublishWorkflow();
                if(!$pw) {
                    $pw = new \NewVision\PublishWorkflowBundle\Entity\PublishWorkflow();
                    $pw->setIsHidden(0);
                    $item->setPublishWorkflow($pw);
                    $entityManager->persist($item);
                }
                $pw->setIsActive(0);
                $entityManager->persist($pw);
            }
            $entityManager->flush();
        } catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add('sonata_flash_error', 'Възникна грешка при изпълнението!');

            return new RedirectResponse($this->admin->generateUrl('list', array('filter' => $this->admin->getFilterParameters())));
        }

        $this->get('session')->getFlashBag()->add('sonata_flash_success', 'Действието е изпълнено успешно!');

        return new RedirectResponse($this->admin->generateUrl('list', array('filter' => $this->admin->getFilterParameters())));
    }

    public function batchActionShow(ProxyQuery $selectedModelQuery)
    {
        if ($this->admin->isGranted('EDIT') === false)
        {
            throw new AccessDeniedException();
        }

        $request = $this->get('request');
        $entityManager = $this->admin->getConfigurationPool()->getContainer()->get('doctrine')->getManager();
        $all = $request->request->get('all_elements');
        // do the merge work here
        try {
            $repo = $entityManager->getRepository($this->admin->getClass());
            if(!$all) {
                $q = $repo->createQueryBuilder('t')
                    ->where('t.id IN(:idx)')
                    ->setParameters(array(':idx' => $request->request->get('idx')))
                    ->getQuery();
                $items = $q->getResult();
            } else {
                $q = $repo->createQueryBuilder('t')
                    ->getQuery();
                $items = $q->getResult();
            }
            foreach ($items as $item) {
                $pw = $item->getPublishWorkflow();
                if(!$pw) {
                    $pw = new \NewVision\PublishWorkflowBundle\Entity\PublishWorkflow();
                    $pw->setIsHidden(0);
                    $item->setPublishWorkflow($pw);
                    $entityManager->persist($item);
                }
                $pw->setIsActive(1);
                $entityManager->persist($pw);
            }
            $entityManager->flush();
        } catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add('sonata_flash_error', 'Възникна грешка при изпълнението!');

            return new RedirectResponse($this->admin->generateUrl('list', array('filter' => $this->admin->getFilterParameters())));
        }

        $this->get('session')->getFlashBag()->add('sonata_flash_success', 'Действието е изпълнено успешно!');

        return new RedirectResponse($this->admin->generateUrl('list', array('filter' => $this->admin->getFilterParameters())));
    }


    /**
     * return the Response object associated to the trash action
     *
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     *
     * @return Response
     */
    public function trashAction()
    {
        if (false === $this->admin->isGranted('LIST')) {
            throw new AccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager();
        $em->getFilters()->disable('softdeleteable');
        $em->getFilters()->enable('softdeleteabletrash');

        $datagrid = $this->admin->getDatagrid();
        $formView = $datagrid->getForm()->createView();
        // set the theme for the current Admin Form
        $this->get('twig')->getExtension('form')->renderer->setTheme($formView, $this->admin->getFilterTheme());

        return $this->render('NewVisionCoreBundle:Admin:trash.html.twig', array(
            'action'     => 'trash',
            'form'       => $formView,
            'datagrid'   => $datagrid,
            'csrf_token' => $this->getCsrfToken('sonata.batch'),
        ));
    }


    public function untrashAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $em->getFilters()->disable('softdeleteable');
        $em->getFilters()->enable('softdeleteabletrash');

        $id     = $request->get($this->admin->getIdParameter());
        $object = $this->admin->getObject($id);
        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        if ($request->getMethod() == 'POST') {
            // check the csrf token
            $this->validateCsrfToken('sonata.untrash');
            try {
                $object->setDeletedAt(null);
                $this->admin->update($object);
                $this->addFlash('sonata_flash_info', $this->get('translator')->trans('flash_untrash_successfull', array(), 'NewVisionCoreBundle'));
            } catch (ModelManagerException $e) {
                if ($this->isXmlHttpRequest()) {
                    return $this->renderJson(array('result' => 'error'));
                }
                $this->addFlash('sonata_flash_info', $this->get('translator')->trans('flash_untrash_error', array(), 'NewVisionCoreBundle'));
            }
            return new RedirectResponse($this->admin->generateUrl('list'));
        }

        return $this->render('NewVisionCoreBundle:Admin:untrash.html.twig', array(
            'object'     => $object,
            'action'     => 'untrash',
            'csrf_token' => $this->getCsrfToken('sonata.untrash')
        ));
    }

    public function deleteAction($id, Request $request = null)
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->resolveRequest($request);
        $id      = $request->get($this->admin->getIdParameter());

        $object  = $this->admin->getObject($id);
        //chech if user try to delete from trash
        if (!$object) {
            $em->getFilters()->disable('softdeleteable');
            $em->getFilters()->enable('softdeleteabletrash');
            $object  = $this->admin->getObject($id);
        }

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        if (false === $this->admin->isGranted('DELETE', $object)) {
            throw new AccessDeniedException();
        }

        if ($this->getRestMethod($request) == 'DELETE') {
            // check the csrf token
            $this->validateCsrfToken('sonata.delete', $request);

            try {
                $this->admin->delete($object);

                if ($this->isXmlHttpRequest($request)) {
                    return $this->renderJson(array('result' => 'ok'), 200, array(), $request);
                }

                $this->addFlash(
                    'sonata_flash_success',
                    $this->admin->trans(
                        'flash_delete_success',
                        array('%name%' => $this->escapeHtml($this->admin->toString($object))),
                        'SonataAdminBundle'
                    )
                );

            } catch (ModelManagerException $e) {
                $this->handleModelManagerException($e);

                if ($this->isXmlHttpRequest($request)) {
                    return $this->renderJson(array('result' => 'error'), 200, array(), $request);
                }

                $this->addFlash(
                    'sonata_flash_error',
                    $this->admin->trans(
                        'flash_delete_error',
                        array('%name%' => $this->escapeHtml($this->admin->toString($object))),
                        'SonataAdminBundle'
                    )
                );
            }

            return $this->redirectTo($object, $request);
        }

        return $this->render($this->admin->getTemplate('delete'), array(
            'object'     => $object,
            'action'     => 'delete',
            'csrf_token' => $this->getCsrfToken('sonata.delete')
        ), null, $request);
    }

    public function batchActionDelete(ProxyQueryInterface $query)
    {
        if (false === $this->admin->isGranted('DELETE')) {
            throw new AccessDeniedException();
        }

        $modelManager = $this->admin->getModelManager();
        try {
            $this->batchDelete($this->admin->getClass(), $query);
            $this->addFlash('sonata_flash_success', 'flash_batch_delete_success');
        } catch (ModelManagerException $e) {

            $this->handleModelManagerException($e);
            $this->addFlash('sonata_flash_error', 'flash_batch_delete_error');
        }

        return new RedirectResponse($this->admin->generateUrl(
            'list',
            array('filter' => $this->admin->getFilterParameters())
        ));
    }

    public function batchDelete($class, ProxyQueryInterface $queryProxy)
    {
        $queryProxy->select('DISTINCT '.$queryProxy->getRootAlias());

        try {
            $entityManager = $this->getDoctrine()->getManager();

            $hasResults = $this->batchRemove($queryProxy);

            if (!$hasResults) {
                $entityManager->getFilters()->disable('softdeleteable');
                $entityManager->getFilters()->enable('softdeleteabletrash');
                $hasResults = $this->batchRemove($queryProxy);
            }

            $entityManager->flush();
            $entityManager->clear();
        } catch (\PDOException $e) {
            throw new ModelManagerException('', 0, $e);
        } catch (DBALException $e) {
            throw new ModelManagerException('', 0, $e);
        }
    }

    private function batchRemove($queryProxy)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $i = 0;
        foreach ($queryProxy->getQuery()->iterate() as $pos => $object) {
            $entityManager->remove($object[0]);

            if ((++$i % 20) == 0) {
                $entityManager->flush();
                $entityManager->clear();
            }
        }

        return $i;
    }

    /**
     * To keep backwards compatibility with older Sonata Admin code.
     *
     * @internal
     */
    private function resolveRequest(Request $request = null)
    {
        if (null === $request) {
            return $this->getRequest();
        }

        return $request;
    }
}