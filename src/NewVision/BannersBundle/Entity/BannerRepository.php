<?php

namespace NewVision\BannersBundle\Entity;

use Doctrine\ORM\EntityRepository;
use NewVision\PublishWorkflowBundle\PublishWorkflowQueryBuilderTrait;

class BannerRepository extends EntityRepository
{
    use PublishWorkflowQueryBuilderTrait;

    /**
     * Find all by locale
     * @var locale string
     * @var limit integer
     * @var offset integer
     * @return array
     */
    public function findAllByLocale($locale, $limit = null, $offset = null)
    {
        $qb = $this->getPublishWorkFlowQueryBuilder();
        $qb
            ->leftJoin('c.translations', 't')
            ->andWhere('t.locale = :locale')
            ->andWhere('t.title IS NOT NULL')
            ->setParameter('locale', $locale)
            ->setParameter('now', new \DateTime())
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->orderBy('c.rank', 'ASC');
        $query = $qb->getQuery();

        return $query->getResult();
    }

    /**
     * Find one by slug and locale
     * @var slug string
     * @var locale string
     * @return \NewVision\ContentBundle\Entity\Content|null
     */
    public function findOneBySlugAndLocale($slug, $locale)
    {
        $qb = $this->getPublishWorkFlowQueryBuilder(null);
        $qb
            ->leftJoin('c.translations', 't')
            ->andWhere('t.locale = :locale')
            ->andWhere('t.slug = :slug')
            ->andWhere('t.title IS NOT NULL')
            ->setParameter('locale', $locale)
            ->setParameter('slug', $slug)
            ->setParameter('now', new \DateTime());
        $query = $qb->getQuery();
        $results = $query->getResult();

        if (count($results)) {
            return $results[0];
        }

        return;
    }
}
