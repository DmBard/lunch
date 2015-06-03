<?php

namespace Ens\LunchBundle\Controller;

use Ens\LunchBundle\Entity\Lunch;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


/**
 * Lunch controller.
 *
 */
class LunchController extends Controller
{

    protected $categories;
    protected $days ;
    private $dateperiod;

    function __construct()
    {

        $this->days = [
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
        ];
        $this->categories = [
            'Salad',
            'Main_Course',
            'Soup',
            'Dessert',
        ];

        $this->dateperiod = date("d.m.Y", strtotime("last Monday")).'-'.date("d.m.Y", strtotime("Sunday"));
    }

    /**
     * Lists all Lunch entities.
     *
     */
    public function indexAction()
    {
        /** @var ManagerRegistry $em */
        $em = $this->getDoctrine()->getManager();
        $repoLunch = $em->getRepository('EnsLunchBundle:Lunch');

        $entities = $repoLunch->getActiveLunches();
        $user = $this->get('security.token_storage')->getToken()->getUser();

        return $this->render(
            'EnsLunchBundle:Lunch:index.html.twig',
            array(
                'entities' => $entities,
                'days' => $this->days,
                'categories' => $this->categories,
                'user' => $user,
                'dateperiod' => $this->dateperiod,
            )
        );
    }


}
