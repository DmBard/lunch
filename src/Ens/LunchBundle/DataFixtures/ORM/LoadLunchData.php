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
        $lunch_sensio_labs->setCategory($em->merge($this->getReference('category-salad')));
        $lunch_sensio_labs->setDay($em->merge($this->getReference('day-monday')));
        $lunch_sensio_labs->setType('salad');
        $lunch_sensio_labs->setDescription('Салат оливье');
        $lunch_sensio_labs->setCount('0');
        $lunch_sensio_labs->setDayOfWeek('monday');

        $em->persist($lunch_sensio_labs);
        $em->flush();
    }

    public function getOrder()
    {
        return 3; // the order in which fixtures will be loaded
    }
}