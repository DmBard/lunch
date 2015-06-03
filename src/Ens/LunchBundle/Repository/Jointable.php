<?php

namespace Ens\LunchBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Jointable
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Jointable extends EntityRepository
{
//    public function getActiveJoinsOrderedByUsers()
//    {
//        $qb = $this->createQueryBuilder('j');
//
//        $qb->where('j.active = :isActive')
//            ->setParameter('isActive', true)
//            ->orderBy('j.userName');
//
//        $query = $qb->getQuery();
//
//        return $query->getResult();
//    }

    public function getActiveJoinsByOneUserAndFloor($user, $floor)
    {
        $qb = $this->createQueryBuilder('j');

        $qb->where('j.active = :isActive')
            ->andWhere('j.userName = :user')
            ->andWhere('j.floor = :floor')
            ->setParameter('user', $user->getUsername())
            ->setParameter('floor', $floor)
            ->setParameter('isActive', true);

        $query = $qb->getQuery();

        return $query->getResult();
    }

    public function getActiveJoinsByOneUser($user)
    {
        $qb = $this->createQueryBuilder('j');

        $qb->where('j.active = :isActive')
            ->andWhere('j.userName = :user')
            ->setParameter('user', $user->getUsername())
            ->setParameter('isActive', true);

        $query = $qb->getQuery();

        return $query->getResult();
    }

    public function getActiveJoinsByOneLunchAndFloor($lunch, $floor)
    {
        $qb = $this->createQueryBuilder('j');

        $qb->where('j.active = :isActive')
            ->andWhere('j.id_lunch = :lunch')
            ->andWhere('j.floor = :floor')
            ->setParameter('lunch', $lunch->getId())
            ->setParameter('floor', $floor)
            ->setParameter('isActive', true);

        $query = $qb->getQuery();

        return $query->getResult();
    }

    public function getPastUserJoins($nameUser)
    {
        $qb = $this->createQueryBuilder('j');

        $qb->where('j.userName = :name')
            ->setParameter('name', $nameUser);

        $query = $qb->getQuery();

        return $query->getResult();
    }
}
