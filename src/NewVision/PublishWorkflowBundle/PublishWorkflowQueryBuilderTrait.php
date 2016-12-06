<?php
/**
 * This file is part of the NewVisionPublishWorkflowBundle.
 *
 * (c) Nikolay Tumbalev <n.tumbalev@newvision.bg>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NewVision\PublishWorkflowBundle;

/**
 *  Trait returning ready query builder for needed query of PublishWorkflow
 *
 * @package NewVisionPublishWorkflowBundle
 * @author  Nikolay Tumbalev <n.tumbalev@newvision.bg>
 */

trait PublishWorkflowQueryBuilderTrait
{
    /**
     * query builder for needed query of PublishWorkflow
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getPublishWorkFlowQueryBuilder($isHidden = 0)
    {
        if ($isHidden === null) {
            $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.publishWorkflow', 'w')
            ->where('
                w.isActive = 1 AND
                (
                    (w.fromDate IS NULL AND w.toDate IS NULL) OR
                    (w.fromDate <= :now AND w.toDate >= :now) OR
                    (
                        (
                            (w.fromDate IS NOT NULL AND w.fromDate <= :now) AND
                            ((w.toDate >= :now OR w.toDate IS NULL) OR (w.toDate IS NOT NULL AND w.toDate >= :now))
                        )
                    )
                )
            ')
            ->setParameter('now', new \DateTime());
            return $qb;
        }
        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.publishWorkflow', 'w')
            ->where('
                w.isActive = 1 AND
                w.isHidden = :hidden AND
                (
                    (w.fromDate IS NULL AND w.toDate IS NULL) OR
                    (w.fromDate <= :now AND w.toDate >= :now) OR
                    (
                        (w.fromDate IS NOT NULL AND w.fromDate <= :now) AND
                        ((w.toDate >= :now OR w.toDate IS NULL) OR (w.toDate IS NOT NULL AND w.toDate >= :now))
                    )
                )
            ')
            ->setParameter('now', new \DateTime())
            ->setParameter('hidden', $isHidden);

        return $qb;
    }
}
