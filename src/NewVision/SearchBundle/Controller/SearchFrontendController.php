<?php

namespace NewVision\SearchBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Query\ResultSetMapping;

class SearchFrontendController extends Controller
{
    /**
     * @Route("/search", name="search")
     * @Template("NewVisionSearchBundle:Frontend:search.html.twig")
     */
    public function searchAction(Request $request)
    {
        $search = $request->query->get('q');
        $locale = $request->getLocale();

        $em = $this->getDoctrine()->getManager();
        $contentObj = $em->getRepository('NewVisionContentBundle:Content')->findOneById(28);
        if(!$contentObj){
            throw $this->createNotFoundException();
        }

        $services = array();
        $airports = array();

        $bundles = $this->container->getParameter('kernel.bundles');
        if (array_key_exists('NewVisionContentBundle', $bundles)) {
            $airports = $this->search($em, 'NewVisionAirportsBundle:Airport', 'NewVisionAirportsBundle:AirportTranslation', $search, $locale);
        }

        if (array_key_exists('NewVisionServicesBundle', $bundles)) {
            $services = $this->search($em, 'NewVisionServicesBundle:Service', 'NewVisionServicesBundle:ServiceTranslation', $search, $locale);
        }
        $results = array_merge_recursive(
            $services,
            $airports
        );

        $totalResults = count($results);
        $breadCrumbs = array($contentObj->getTitle() => null);

        $dispatcher = $this->get('event_dispatcher');
        $event = new \NewVision\SEOBundle\Event\SeoEvent($contentObj);
        $dispatcher->dispatch('newvision.seo', $event);

        return array(
            'airports'          => $airports,
            'services'          => $services,
            'content'           => $contentObj,
            'search'            => $search,
            'totalResults'      => $totalResults,
            'breadCrumbs'       => $breadCrumbs,
        );
    }

    private function search($em, $entityClass, $i18nClass, $search, $locale)
    {
        $search = mb_strtolower($search,"UTF-8");
        $rsm = new ResultSetMapping();
        $rsm->addEntityResult($entityClass, 't');
        $rsm->addFieldResult('t', 'id', 'id');
        $rsm->addJoinedEntityResult($i18nClass, 'i18n', 't', 'translations');
        $rsm->addFieldResult('i18n', 'i_id', 'id');
        $rsm->addFieldResult('i18n', 'slug', 'slug');
        $rsm->addFieldResult('i18n', 'title', 'title');
        $rsm->addFieldResult('i18n', 'description', 'description');
        $tableName = $em->getClassMetadata($entityClass)->getTableName();
        $tableI18nName = $em->getClassMetadata($i18nClass)->getTableName();


        $query = $em->createNativeQuery(
            "SELECT t.id, i18n.id as i_id, i18n.slug, i18n.title, i18n.description
            FROM {$tableName} t
            LEFT JOIN {$tableI18nName} i18n on t.id=i18n.object_id
            WHERE
            i18n.locale = ?
            AND
            (
                LOWER(i18n.title) REGEXP ?
                OR
                LOWER(i18n.description) REGEXP ?
            )
            ",$rsm
        )
        ->setParameter(1, $locale)
        ->setParameter(2, '[[:<:]]'.$search.'[[:>:]]')
        ->setParameter(3, '[[:<:]]'.$search.'[[:>:]]');

        return $query->getResult();
    }
}
