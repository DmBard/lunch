<?php
// src/Ens/LunchBundle/DataFixtures/ORM/LoadDayData.php

namespace Ens\LunchBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Ens\LunchBundle\Entity\Day;

class LoadDayData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $em)
    {
        $mon = new Day();
        $mon->setName('Monday');

        $tue = new Day();
        $tue->setName('Tuesday');

        $wed = new Day();
        $wed->setName('Wednesday');

        $thu = new Day();
        $thu->setName('Thursday');

        $fri = new Day();
        $fri->setName('Friday');

        $sat = new Day();
        $sat->setName('Saturday');

        $sun = new Day();
        $sun->setName('Sunday');

        $em->persist($mon);
        $em->persist($tue);
        $em->persist($wed);
        $em->persist($thu);
        $em->persist($fri);
        $em->persist($sat);
        $em->persist($sun);
        $em->flush();

        $this->addReference('day-monday', $mon);
        $this->addReference('day-tuesday', $tue);
        $this->addReference('day-wednesday', $wed);
        $this->addReference('day-thursday', $thu);
        $this->addReference('day-friday', $fri);
        $this->addReference('day-saturday', $sat);
        $this->addReference('day-sunday', $sun);
    }

    public function getOrder()
    {
        return 2; // the order in which fixtures will be loaded
    }
}