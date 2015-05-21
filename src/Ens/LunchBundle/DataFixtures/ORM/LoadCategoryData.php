<?php
// src/Ens/LunchBundle/DataFixtures/ORM/LoadCategoryData.php

namespace Ens\LunchBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Ens\LunchBundle\Entity\Category;

class LoadCategoryData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $em)
    {
        $salad = new Category();
        $salad->setName('Salad');

        $soup = new Category();
        $soup->setName('Soup');

        $dessert = new Category();
        $dessert->setName('Dessert');

        $main_course = new Category();
        $main_course->setName('Main Course');

        $em->persist($salad);
        $em->persist($soup);
        $em->persist($dessert);
        $em->persist($main_course);
        $em->flush();

        $this->addReference('category-salad', $salad);
        $this->addReference('category-soup', $soup);
        $this->addReference('category-dessert', $dessert);
        $this->addReference('category-mainCourse', $main_course);
    }

    public function getOrder()
    {
        return 1; // the order in which fixtures will be loaded
    }
}