<?php

namespace Ens\LunchBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('EnsLunchBundle:Default:index.html.twig', array('name' => $name));
    }
}
