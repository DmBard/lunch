<?php

namespace Ens\LunchBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * CategoryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CategoryRepository extends EntityRepository
{
    public function getWithLunches()
    {
        $query = $this->getEntityManager()->createQuery(
            'SELECT c FROM EnsLunchBundle:Category c LEFT JOIN c.lunches j WHERE j.day_of_week > :day'
        )->setParameter('day', "friday");

        return $query->getResult();
    }
}
