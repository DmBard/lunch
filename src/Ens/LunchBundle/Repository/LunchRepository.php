<?php

namespace Ens\LunchBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * LunchRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class LunchRepository extends EntityRepository
{
    public function getCategoryDayLunches($currentCategory = null, $currentDay = null)
    {
        $qb = $this->createQueryBuilder('l');

        if ($currentDay) {
            $qb->where('l.day = :dayOfWeek')
                ->setParameter('dayOfWeek', $currentDay);
        }

        if ($currentCategory) {
            $qb->andWhere('l.categories = :category_i')
                ->setParameter('category_i', $currentCategory);
        }

        $query = $qb->getQuery();

        return $query->getResult();
    }

}
