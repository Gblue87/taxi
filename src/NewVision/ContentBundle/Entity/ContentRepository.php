<?php
namespace NewVision\ContentBundle\Entity;

use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use NewVision\PublishWorkflowBundle\PublishWorkflowQueryBuilderTrait;

class ContentRepository extends NestedTreeRepository
{
    use PublishWorkflowQueryBuilderTrait;

    public function create()
    {
        $item = new Content();

        return $item;
    }

    public function save(Content $item, $andFlush = true)
    {
        $this->em->persist($item);
        if ($andFlush) {
            $this->em->flush();
        }

        return $item;
    }

    public function delete($item)
    {
        $this->repo->removeFromTree($item);

        return $this;
    }

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
            ->andWhere('c.parent = :rootId')
            ->andWhere('t.title IS NOT NULL')
            ->setParameter('rootId', $rootId)
            ->orderBy('c.lft', 'ASC');
        $query = $qb->getQuery();

        return $query->getResult();
    }
}
