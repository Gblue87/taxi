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
        $contentObj = $em->getRepository('NewVisionContentBundle:Content')->findOneById(10);
        if(!$contentObj){
            throw $this->createNotFoundException();
        }

        $contentPages = array();
        $products = array();
        $productCategories = array();
        $news = array();
        $newsCategories = array();
        $services = array();
        $serviceCategories = array();
        $careers = array();
        $galleries = array();

        $bundles = $this->container->getParameter('kernel.bundles');
        if (array_key_exists('NewVisionContentBundle', $bundles)) {
            $contentPages = $this->search($em, 'NewVisionContentBundle:Content', 'NewVisionContentBundle:ContentTranslation', $search, $locale);
        }

        if (array_key_exists('NewVisionProductsBundle', $bundles)) {
            $products = $this->search($em, 'NewVisionProductsBundle:Product', 'NewVisionProductsBundle:ProductTranslation', $search, $locale);
            $productCategories = $this->search($em, 'NewVisionProductsBundle:ProductCategory', 'NewVisionProductsBundle:ProductCategoryTranslation', $search, $locale);
        }

        if (array_key_exists('NewVisionNewsBundle', $bundles)) {
            $news = $this->search($em, 'NewVisionNewsBundle:News', 'NewVisionNewsBundle:NewsTranslation', $search, $locale);
            $newsCategories = $this->search($em, 'NewVisionNewsBundle:NewsCategory', 'NewVisionNewsBundle:NewsCategoryTranslation', $search, $locale);
        }

        if (array_key_exists('NewVisionServicesBundle', $bundles)) {
            $services = $this->search($em, 'NewVisionServicesBundle:Service', 'NewVisionServicesBundle:ServiceTranslation', $search, $locale);
            $serviceCategories = $this->search($em, 'NewVisionServicesBundle:ServiceCategory', 'NewVisionServicesBundle:ServiceCategoryTranslation', $search, $locale);
        }

        if (array_key_exists('NewVisionCareersBundle', $bundles)) {
            $careers = $this->search($em, 'NewVisionCareersBundle:Career', 'NewVisionCareersBundle:CareerTranslation', $search, $locale);
        }

        if (array_key_exists('NewVisionGalleriesBundle', $bundles)) {
            $galleries = $this->search($em, 'NewVisionGalleriesBundle:Gallery', 'NewVisionGalleriesBundle:GalleryTranslation', $search, $locale);
        }

        $results = array_merge_recursive(
            $contentPages,
            $products,
            $productCategories,
            $news,
            $newsCategories,
            $services,
            $serviceCategories,
            $careers,
            $galleries
        );

        $totalResults = count($results);
        $breadCrumbs = array($contentObj->getTitle() => null);

        $dispatcher = $this->get('event_dispatcher');
        $event = new \NewVision\SEOBundle\Event\SeoEvent($contentObj);
        $dispatcher->dispatch('newvision.seo', $event);

        return array(
            'contentPages'      => $contentPages,
            'products'          => $products,
            'productCategories' => $productCategories,
            'news'              => $news,
            'newsCategories'    => $newsCategories,
            'services'          => $services,
            'serviceCategories' => $serviceCategories,
            'careers'           => $careers,
            'galleries'         => $galleries,
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
        $rsm->addFieldResult('t', 'lvl', 'lvl');
        $rsm->addFieldResult('t', 'isSystem', 'isSystem');
        $rsm->addJoinedEntityResult($i18nClass, 'i18n', 't', 'translations');
        $rsm->addFieldResult('i18n', 'i_id', 'id');
        $rsm->addFieldResult('i18n', 'slug', 'slug');
        $rsm->addFieldResult('i18n', 'title', 'title');
        $rsm->addFieldResult('i18n', 'description', 'description');
        $tableName = $em->getClassMetadata($entityClass)->getTableName();
        $tableI18nName = $em->getClassMetadata($i18nClass)->getTableName();

        if($entityClass == 'NewVisionContentBundle:Content') {
            $query = $em->createNativeQuery(
                "SELECT t.id, i18n.id as i_id, i18n.slug, i18n.title, t.is_system
                FROM {$tableName} t left join {$tableI18nName} i18n on t.id=i18n.object_id
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
        } elseif ($entityClass == 'NewVisionProductsBundle:Product') {
            $query = $em->createNativeQuery(
                "SELECT t.id, i18n.id as i_id, i18n.slug, i18n.title, pc.productcategory_id
                FROM {$tableName} t
                LEFT JOIN {$tableI18nName} i18n on t.id=i18n.object_id
                LEFT JOIN products_categories pc on t.id=pc.product_id
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
        } elseif ($entityClass == 'NewVisionNewsBundle:News') {
            $query = $em->createNativeQuery(
                "SELECT t.id, i18n.id as i_id, i18n.slug, i18n.title, nc.newscategory_id
                FROM {$tableName} t
                LEFT JOIN {$tableI18nName} i18n on t.id=i18n.object_id
                LEFT JOIN news_categories_m2m nc on t.id=nc.news_id
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
        }
        elseif ($entityClass == 'NewVisionServicesBundle:Service') {
            $query = $em->createNativeQuery(
                "SELECT t.id, i18n.id as i_id, i18n.slug, i18n.title, nc.newscategory_id
                FROM {$tableName} t
                LEFT JOIN {$tableI18nName} i18n on t.id=i18n.object_id
                LEFT JOIN news_categories_m2m nc on t.id=nc.news_id
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
        }
        else {
            $query = $em->createNativeQuery(
                "SELECT t.id, i18n.id as i_id, i18n.slug, i18n.title
                FROM {$tableName} t left join {$tableI18nName} i18n on t.id=i18n.object_id
                WHERE
                i18n.locale = ?
                AND
                (
                    LOWER(i18n.title) REGEXP ?
                )
                ",$rsm
            )
            ->setParameter(1, $locale)
            ->setParameter(2, '[[:<:]]'.$search.'[[:>:]]');
        }
        return $query->getResult();
    }
}
