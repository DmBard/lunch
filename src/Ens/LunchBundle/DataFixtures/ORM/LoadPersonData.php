<?php
// src/Ens/LunchBundle/DataFixtures/ORM/LoadPersonData.php

namespace Ens\LunchBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Ens\LunchBundle\Entity\Category;
use Ens\LunchBundle\Entity\Person;

class LoadPersonData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $em)
    {
        $ivan = new Person();
        $ivan->setName('Ivan');

        $kate = new Person();
        $kate->setName('Kate');

        $admin = new Person();
        $admin->setName('Main Course');

        $em->persist($ivan);
        $em->persist($kate);
        $em->persist($admin);
        $em->flush();

        $this->addReference('person-ivan', $ivan);
        $this->addReference('person-kate', $kate);
        $this->addReference('person-admin', $admin);
    }

    public function getOrder()
    {
        return 1; // the order in which fixtures will be loaded
    }
}