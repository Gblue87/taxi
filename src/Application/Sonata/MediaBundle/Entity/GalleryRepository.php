<?php 
namespace Application\Sonata\MediaBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class GalleryRepository extends EntityRepository
{
    public function getAll($locale, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('n')
            ->leftJoin('n.translations', 't')
            ->where('n.enabled = :enabled and t.locale = :locale')
            ->orderBy('n.rank', 'ASC')
            ->setParameters(array('enabled' => 1, 'locale' => $locale))
            ->setMaxResults($limit)
            ->setFirstResult($offset);
        $query = $qb->getQuery();
        return $query->getResult();
    }

    public function findAllByLocaleAndContext($locale, $context, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('n')
            ->leftJoin('n.translations', 't')
            ->where('n.enabled = :enabled and t.locale = :locale and n.context = :context')
            ->orderBy('n.rank', 'ASC')
            ->setParameters(array('enabled' => 1, 'locale' => $locale, 'context' => $context))
            ->setMaxResults($limit)
            ->setFirstResult($offset);
        $query = $qb->getQuery();
        return $query->getResult();
    }

    
}