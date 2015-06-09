<?php

namespace Ens\LunchBundle\Controller;

use Ens\LunchBundle\Entity\Jointable;
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
    protected $days;
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

        if (date('D') == 'Mon') {
            $this->dateperiod = date("d.m.Y", strtotime("Monday")).'-'.date("d.m.Y", strtotime("Sunday"));
        } else {
            $this->dateperiod = date("d.m.Y", strtotime("last Monday")).'-'.date("d.m.Y", strtotime("Sunday"));
        }
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
        $repoUser = $em->getRepository('EnsLunchBundle:User');
        $repoDocs = $em->getRepository('EnsLunchBundle:Document');
        $repoJoins = $em->getRepository('EnsLunchBundle:Jointable');
        $documents = $repoDocs->findAll();

        //Get the list of names of all uploading files and search the dateperiod in names
        $docNames = [];
        foreach ($documents as $doc) {
            array_push($docNames, $doc->getName());
        }

        //If no match is found will set all active lunches is not active
        $isNewMenu = in_array($this->dateperiod, $docNames);
        if (!$isNewMenu) {
            $lunches = $repoLunch->getActiveLunches();
            foreach ($lunches as $lunch) {
                $lunch->setActive(0);
                $em->persist($lunch);
            }
        }
        $em->flush();

        $user = $this->get('security.token_storage')->getToken()->getUser();

        if (!empty($_POST['def_action'])) {
            $foundUser = $repoUser->findOneBy(['username' => $user->getUsername()]);
            $request = $this->get('request');
            $def_action = $request->request->get('def_action');
            $foundUser->setName($user->getName());
            $foundUser->setDefaultAction($def_action);
            $em->persist($foundUser);
        }

        //Show warning if the user has not the order
        $warning = '';
        $warningText = 'You have not done the order on the current week';
        $joins = $repoJoins->getActiveJoinsByOneUser($user);
        if (count($joins) == 0) {
            $warning = $warningText;
        } else {
            foreach ($joins as $join) {
                $myLunch = $repoLunch->findOneBy(
                    array(
                        'id' => $join->getIdLunch(),
                    )
                );
                if (!$myLunch->getActive()) {
                    $warning = $warningText;
                    break;
                }
            }
        }

        $entities = $repoLunch->getActiveLunches();
        $em->flush();

        $isShowAdminLink = in_array('ROLE_ADMIN', $user->getRoles());

        return $this->render(
            'EnsLunchBundle:Lunch:index.html.twig',
            array(
                'entities' => $entities,
                'days' => $this->days,
                'categories' => $this->categories,
                'user' => $user,
                'dateperiod' => $this->dateperiod,
                'isShowAdminLink' => $isShowAdminLink,
                'warning' => $warning,
            )
        );
    }

    public function userOrderAction()
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $joins = $this->getDoctrine()->getManager()->getRepository(
            'EnsLunchBundle:Jointable'
        )->getActiveJoinsByOneUser($user);
        $lunches = $this->getDoctrine()->getManager()->getRepository('EnsLunchBundle:Lunch')->getActiveLunches();

        return $this->render(
            'EnsLunchBundle:Lunch:order.html.twig',
            array(
                'days' => $this->days,
                'categories' => $this->categories,
                'lunches' => $lunches,
                'user' => $user,
                'joins' => $joins,
                'dateperiod' => $this->dateperiod,
            )
        );
    }

    /**
     * Displays a order form
     *
     */
    public function orderAction()
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $searchUser = $this->getDoctrine()->getManager()->getRepository(
            'EnsLunchBundle:User'
        )->findOneBy(array('username' => $user->getUsername()));

        $request = $this->get('request');
        $randomMode = $request->request->get('random_mode');

        //Add data into jointable
        if (!$randomMode) {
            $this->addDataInJointable($searchUser);
        } else {
            $this->addRandomDataInJointable($searchUser);
        }

        $joins = $this->getDoctrine()->getManager()->getRepository(
            'EnsLunchBundle:Jointable'
        )->getActiveJoinsByOneUser($user);

        $lunches = $this->getDoctrine()->getManager()->getRepository('EnsLunchBundle:Lunch')->getActiveLunches();

        return $this->render(
            'EnsLunchBundle:Lunch:order.html.twig',
            array(
                'days' => $this->days,
                'categories' => $this->categories,
                'lunches' => $lunches,
                'user' => $user,
                'joins' => $joins,
                'dateperiod' => $this->dateperiod,
            )
        );
    }

    /**
     * @param $user
     */
    private function addDataInJointable($user)
    {
        $em = $this->getDoctrine()->getManager();
        $userName = $user->getUsername();

        $pastJoins = $em->getRepository('EnsLunchBundle:Jointable')->getPastUserJoins($userName);

        foreach ($pastJoins as $item) {
            $item->setActive(0);
            $em->persist($item);
        }

        $request = $this->get('request');
        $floor = $request->request->get('floor');
        $user->setFloor($floor);
        $em->persist($user);

        //insert a user choice in the JoinTable
        foreach ($this->categories as $category) {
            foreach ($this->days as $day) {
                $join = new Jointable();
                $idLunch = $_POST[$category][$day];
                $join->setIdLunch($idLunch);
                $join->setUserName($userName);
                $join->setFloor($floor);
                $join->setName($user->getName());
                $join->setActive(1);
                $em->persist($join);
            }
        }
        $em->flush();
    }

    private function addRandomDataInJointable($user)
    {
        $em = $this->getDoctrine()->getManager();
        $userName = $user->getUserName();

        $pastJoins = $em->getRepository('EnsLunchBundle:Jointable')->getPastUserJoins($userName);

        foreach ($pastJoins as $item) {
            $item->setActive(0);
            $em->persist($item);
        }
        $request = $this->get('request');
        $floor = $request->request->get('floor');
        $user->setFloor($floor);
        $em->persist($user);

        //insert a user choice in the JoinTable
        foreach ($this->categories as $category) {
            foreach ($this->days as $day) {
                $joinTable = new Jointable();
                $lunches = $em->getRepository('EnsLunchBundle:Lunch')->getActiveCategoryAndDayLunches($category, $day);
                $idLunch = $lunches[rand(0, count($lunches) - 1)]->getId();
                $joinTable->setIdLunch($idLunch);
                $joinTable->setUserName($userName);
                $joinTable->setFloor($floor);
                $joinTable->setName($user->getName());
                $joinTable->setActive(1);
                $em->persist($joinTable);
            }
        }
        $em->flush();
    }
}
