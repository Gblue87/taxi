<?php
namespace NewVision\MenuBundle\Entity;

use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use NewVision\PublishWorkflowBundle\PublishWorkflowQueryBuilderTrait;

class MenuRepository extends NestedTreeRepository
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
            ->andWhere('c.lvl = 0')
            ->andWhere('t.title IS NOT NULL')
            ->setParameter('locale', $locale)
            ->setParameter('now', new \DateTime())
            ->setMaxResults($limit)
            ->orderBy('c.lft', 'ASC')
            ->setFirstResult($offset);
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

    /**
     * Find all children by given parent id
     * @param  integer $rootId
     * @return array
     */
    public function findAllChildren($rootId)
    {
        $qb = $this->getPublishWorkFlowQueryBuilder();
        $qb
            ->leftJoin('c.translations', 't')
            ->andWhere('w.isHidden = 0')
            ->andWhere('c.parent = :rootId')
            ->andWhere('t.title IS NOT NULL')
            ->setParameter('rootId', $rootId)
            ->orderBy('c.lft', 'ASC');
        $query = $qb->getQuery();

        return $query->getResult();
    }
}
