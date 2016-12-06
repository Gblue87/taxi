<?php

namespace NewVision\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 */
class HistoryCRUDController extends BatchCRUDController
{
    public function historyAction($id = null, Request $reqest = null)
    {
    	$this->admin->buildTabMenu('history');
    	$object = $this->admin->getObject($id);
        $em = $this->admin->getConfigurationPool()->getContainer()->get('doctrine')->getManager();

    	$locale = $this->admin->getRequest()->query->get('locale', null);


    	$repo = $em->getRepository('Gedmo\Loggable\Entity\LogEntry'); // we use default log entry class
    	if($locale !== null) {
    		$translations = $object->getTranslations();
    		if($translations[$locale] !== null) {
    			$logs = $repo->getLogEntries($translations[$locale]);
    		} else {
    			$logs = array();
    		}
    	} else {
    		$logs = $repo->getLogEntries($object);
    	}

        return $this->render('NewVisionCoreBundle:Admin:history.html.twig', array(
            'object' => $object,
            'revisions' => $logs,
            'action' => 'history',
            'locale' => $locale
        ));
    }

    public function historyViewRevisionAction($id = null, $revision = null, Request $reqest = null)
    {
        $em = $this->admin->getConfigurationPool()->getContainer()->get('doctrine')->getManager();
        $object = $this->admin->getObject($id);
    	$showObject = $this->loadObject($object, $revision, $em);

		$serializer = \JMS\Serializer\SerializerBuilder::create()->build();
		$jsonObject = $serializer->serialize($showObject, 'json');
		$objectToArray =  json_decode($jsonObject, true);


        return $this->render('NewVisionCoreBundle:Admin:show.html.twig', array(
            'action' => 'show',
            'object' => $object,
            'objectToArray' => $objectToArray,
            'revision' => $revision
        ));
    }

    public function historyRevertToRevisionAction($id = null, $revision = null)
    {
        $em = $this->admin->getConfigurationPool()->getContainer()->get('doctrine')->getManager();
        $object = $this->admin->getObject($id);

    	$showObject = $this->loadObject($object, $revision, $em);

        $em->persist($showObject);
        $em->flush();

		return new RedirectResponse($this->admin->generateObjectUrl('edit', $object));
    }

    private function loadObject($object, $revision, $em)
    {
        $locale = $this->admin->getRequest()->query->get('locale', null);


        $repo = $em->getRepository('Gedmo\Loggable\Entity\LogEntry'); // we use default log entry class
        if($locale !== null) {
            $translations = $object->getTranslations();
            $showObject = $translations[$locale];
        } else {
            $showObject = $object;
        }
        $repo->revert($showObject, $revision);

        return $showObject;
    }
}