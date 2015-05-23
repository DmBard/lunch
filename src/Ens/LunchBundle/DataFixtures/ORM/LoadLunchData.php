<?php
// src/Ens/LunchBundle/DataFixtures/ORM/LoadLunchData.php

namespace Ens\LunchBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Ens\LunchBundle\Entity\Lunch;

class LoadLunchData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $em)
    {
        $lunch_sensio_labs = new Lunch();
        $lunch_sensio_labs->setCategories("Salad");
        $lunch_sensio_labs->setDay("Monday");
        $lunch_sensio_labs->setDescription("Оливье");
        $lunch_sensio_labs->setCount(0);

        $em->persist($lunch_sensio_labs);
        $em->flush();
    }

    public function getOrder()
    {
        return 3; // the order in which fixtures will be loaded
    }
}