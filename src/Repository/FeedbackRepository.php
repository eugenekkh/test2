<?php

namespace App\Repository;

use App\Model\Feedback;

/**
 * FeedbackRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FeedbackRepository extends \Doctrine\ORM\EntityRepository
{
    public function getPublishedFeedbacks($sort, $order)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb
            ->select('f')
            ->from(Feedback::class, 'f')
            ->where('f.published = true')
            ->orderBy('f.' . $sort, $order);
        ;

        return $qb->getQuery()->getResult();
    }
}
