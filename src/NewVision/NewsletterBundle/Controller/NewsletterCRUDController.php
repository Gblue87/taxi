<?php

namespace NewVision\NewsletterBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use NewVision\CoreBundle\Controller\TreeCRUDController;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Sonata\AdminBundle\Exception\ModelManagerException;

class NewsletterCRUDController extends TreeCRUDController
{
    public function mailChimpStatusAction() {
		$em = $this->getDoctrine()->getManager();

		$mailChimpObject = $em->getRepository('NewVisionMailChimpBundle:MailChimp')->findOneById(1);
		if ($mailChimpObject != null && $mailChimpObject->getListId() != null && $mailChimpObject->getListId() != '') {
			$mc = new \NewVision\MailChimpBundle\Services\MailChimp($mailChimpObject->getApiKey());
			$listMembers = $mc->getList($mailChimpObject->getListId())->getAllListMembers();

			$newsletterRepo = $em->getRepository('NewVisionNewsletterBundle:Newsletter');
			foreach ($listMembers->members as $mcMember) {
				$newsletter = $newsletterRepo->findOneByEmail($mcMember->email_address);
				if ($newsletter != null) {
					$newsletter->setMailChimpStatus($mcMember->status);
					$em->persist($newsletter);
					$em->flush();
				}
			}
		}

		return $this->redirect('list');
    }
}
