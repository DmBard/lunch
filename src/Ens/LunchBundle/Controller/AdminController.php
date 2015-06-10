<?php

/**
 * Created by PhpStorm.
 * User: d.baryshev
 * Date: 02.06.2015
 * Time: 11:16
 */
namespace Ens\LunchBundle\Controller;

use Ens\LunchBundle\Entity\Document;
use Ens\LunchBundle\Entity\Jointable;
use Ens\LunchBundle\Entity\Lunch;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller
{
    protected $categories;
    protected $days;
    private $dateperiod;
    private $pathDocuments;
    private $pathOrders;

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

        $this->get('service_container')->getParameter('upload_path');
        $this->pathDocuments = $path['documents'];
        $this->pathOrders = $path['orders'];
    }

    public function adminIndexAction()
    {
        /** @var ManagerRegistry $em */
        $em = $this->getDoctrine()->getManager();
        $repoDocs = $em->getRepository('EnsLunchBundle:Document');
        $documents = $repoDocs->findAll();

        //Set the remaining time
        $remainingTime = '';
        if ($documents) {
            $lastDoc = $repoDocs->findBy(array(), array('id' => 'DESC'));
            $doc = $repoDocs->findOneBy(array('id' => $lastDoc[0]));
            $planingTime = $doc->getTime();
            $currentTime = new \DateTime();
            $remainingTime = 'Remaining time to generate files of orders: '.$planingTime->diff($currentTime)->format(
                    '%d days, %h hours, %I minutes'
                );
        }

        //Get the list of names of all uploading files and search the dateperiod in names
        $docNames = [];
        foreach ($documents as $doc) {
            array_push($docNames, $doc->getName());
        }
        $isNewMenu = in_array($this->dateperiod, $docNames);

        //If no match is found will show the warning
        $warning = '';
        if (!$isNewMenu) {
            $warning = 'The menu is not updated. Please, update it!';
            $remainingTime = '';
        }

        return $this->render(
            'EnsLunchBundle:Lunch:admin_index.html.twig',
            array(
                'dateperiod' => $this->dateperiod,
                'warning' => $warning,
                'remainingTime' => $remainingTime,
            )
        );
    }

    //user profile
    public function profileAction()
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        return $this->render(
            'EnsLunchBundle:Lunch:user_profile.html.twig',
            array(
                'user' => $user,
            )
        );
    }

    //upload xls file and parse it
    public function uploadAction(Request $request)
    {
        $document = new Document();
        $form = $this->createFormBuilder($document)
            ->add('file')
            ->add('time')
            ->add('submit', 'submit', array('label' => 'Create'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $document->upload();
            $document->setName($this->dateperiod);

            $em->persist($document);
            $em->flush();

            //parse a xls file and form the menu
            try {
                $xlsParser = $this->get('ens_lunch.xls_manager');
                $xlsParser->parseXlsFile();
            } catch (\Exception $e) {
                $this->render('EnsLunchBundle:Lunch:error.html.twig');
            }

            return $this->redirectToRoute('ens_lunch_show_all');
        }

        return $this->render(
            'EnsLunchBundle:Lunch:new.html.twig',
            array(
                'form' => $form->createView(),
            )
        );
    }

    //show all dishes
    public function showAllAction()
    {
        /** @var ManagerRegistry $em */
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('EnsLunchBundle:Lunch');

        $entities = $repo->findAll();

        return $this->render(
            'EnsLunchBundle:Lunch:all.html.twig',
            array(
                'entities' => $entities
            )
        );
    }

    public function showAllUsersAction()
    {
        /** @var ManagerRegistry $em */
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('EnsLunchBundle:User');

        $entities = $repo->findAll();

        return $this->render(
            'EnsLunchBundle:Lunch:all_users.html.twig',
            array(
                'entities' => $entities
            )
        );
    }

    public function generateFilesAction()
    {
        $floorList = [
            'floor_4',
            'floor_5',
        ];

        foreach ($floorList as $floor) {
            $users = $this->getDoctrine()->getManager()->getRepository('EnsLunchBundle:User')->findBy(
                ['floor' => $floor]
            );

            //array of user choices
            $userChoices = [];
            $names = [];
            foreach ($users as $itemUser) {
                $joinList = $this->getDoctrine()->getManager()->getRepository(
                    'EnsLunchBundle:Jointable'
                )->getActiveJoinsByOneUser($itemUser);

                $lunchList = [];
                if ($joinList) {
                    foreach ($joinList as $joinItem) {
                        array_push(
                            $lunchList,
                            $this->getDoctrine()->getManager()->getRepository(
                                'EnsLunchBundle:Lunch'
                            )->findOneBy(array('id' => $joinItem->getIdLunch(), 'active' => 1))
                        );
                    }
                }

                //lunch list is empty, because user didn't do the order, then default mode is on
                //default mode (action) - delete user order
                if ((!$lunchList || $joinList == null || $lunchList[0] == null) && ($itemUser->getDefaultAction() == 1)
                ) {
                    continue;
                }

                //lunch list is empty, because user didn't do the order, then default mode is on
                //default mode (action) - random choice
                elseif ((!$lunchList || $joinList == null || $lunchList[0] == null) && ($itemUser->getDefaultAction(
                        ) == 2)
                ) {
                    $randomChoices = $this->setRandomChoice(
                        $itemUser,
                        $floor
                    );
                    foreach ($randomChoices as $randomChoice) {
                        array_push(
                            $userChoices,
                            $randomChoice
                        );
                    }
                }
                //lunch list is empty, because user didn't do the order, then default mode is on
                //default mode (action) - previous choice
                elseif ((!$lunchList || $joinList == null || $lunchList[0] == null) && ($itemUser->getDefaultAction(
                        ) == 3)
                ) {

                    $previousChoices = $this->setPreviousChoice(
                        $joinList,
                        $itemUser,
                        $floor
                    );
                    foreach ($previousChoices as $previousChoice) {
                        array_push(
                            $userChoices,
                            $previousChoice
                        );
                    }
                } //lunch list is not empty
                else {
                    foreach ($this->categories as $itemCategory) {
                        foreach ($this->days as $itemDay) {
                            foreach ($lunchList as $itemLunch) {

                                if (($itemLunch->getCategories() == $itemCategory) and ($itemLunch->getDay(
                                        ) == $itemDay)
                                ) {
                                    array_push($userChoices, $itemLunch->getCount());
                                }
                            }
                        }
                    }
                }
                array_push($names, $itemUser->getName());
            }

            //create order and menu xls files
            $xlsWriter = $this->get('ens_lunch.xls_manager');
            $xlsWriter->writeOrderXlsFile($names, $userChoices, $floor);
            $xlsWriter->writeMenuXlsFile($floor);
        }

        $fileNames = ['floor_4_menu', 'floor_5_menu', 'floor_4_order', 'floor_5_order'];

        return $this->render(
            'EnsLunchBundle:Lunch:order_files.html.twig',
            array(
                'fileNames' => $fileNames
            )
        );
    }

    /**
     * @param $em
     * @param $userName
     * @return array
     */
    private function clearPastJoins($em, $userName)
    {
        $pastJoins = $em->getRepository('EnsLunchBundle:Jointable')->getPastUserJoins($userName);

        if ($pastJoins) {
            foreach ($pastJoins as $item) {
                $item->setActive(0);
                $em->persist($item);
            }

            return array($pastJoins);
        } else {
            return;
        }
    }

    /**
     * @param $em
     * @param $itemDay
     * @param $itemCategory
     * @param $itemLunch
     * @param $userName
     * @param $itemUser
     * @return array
     */
    private function createNewJoins($em, $itemDay, $itemCategory, $itemLunch, $userName, $itemUser, $floor)
    {
        $join = new Jointable();
        $lunch = $em->getRepository('EnsLunchBundle:Lunch')->findOneBy(
            array(
                'day' => $itemDay,
                'categories' => $itemCategory,
                'active' => 1,
                'count' => $itemLunch->getCount()
            )
        );
        $join->setIdLunch($lunch->getId());
        $join->setUserName($userName);
        $join->setName($itemUser->getName());
        $join->setFloor($floor);
        $join->setActive(1);
        $em->persist($join);
    }

    /**
     * @param $joinList
     * @param $itemUser
     * @param $floor
     * @return array
     */
    private function setPreviousChoice($joinList, $itemUser, $floor)
    {

        if ($joinList) {
            $lunches = [];
            $userChoices = [];
            foreach ($joinList as $joinItem) {
                array_push(
                    $lunches,
                    $this->getDoctrine()->getManager()->getRepository('EnsLunchBundle:Lunch')->findOneBy(
                        array('id' => $joinItem->getIdLunch())
                    )
                );
            }
            $em = $this->getDoctrine()->getManager();
            $userName = $itemUser->getUserName();

            $this->clearPastJoins($em, $userName);

            foreach ($this->categories as $itemCategory) {
                foreach ($this->days as $itemDay) {
                    foreach ($lunches as $itemLunch) {
                        if (($itemLunch->getCategories() == $itemCategory) and ($itemLunch->getDay() == $itemDay)
                        ) {
                            array_push($userChoices, $itemLunch->getCount());

                            $this->createNewJoins(
                                $em,
                                $itemDay,
                                $itemCategory,
                                $itemLunch,
                                $userName,
                                $itemUser,
                                $floor
                            );
                        }
                    }
                }
            }
            $em->flush();

            return $userChoices;
        } else {
            return;
        }
    }

    /**
     * @param $itemUser
     * @param $floor
     * @return array
     */
    private function setRandomChoice($itemUser, $floor)
    {
        $userChoices = [];
        $em = $this->getDoctrine()->getManager();

        $userName = $itemUser->getUserName();

        $joins = $em->getRepository('EnsLunchBundle:Jointable')->findBy(
            [
                'userName' => $userName,
            ]
        );
        if ($joins) {
            $this->clearPastJoins($em, $userName);
        }

        foreach ($this->categories as $itemCategory) {
            foreach ($this->days as $itemDay) {
                $lunches = $this->getDoctrine()->getManager()->getRepository(
                    'EnsLunchBundle:Lunch'
                )->getActiveCategoryAndDayLunches($itemCategory, $itemDay);
                $itemLunch = $lunches[rand(0, count($lunches) - 1)];
                array_push($userChoices, $itemLunch->getCount());

                $this->createNewJoins(
                    $em,
                    $itemDay,
                    $itemCategory,
                    $itemLunch,
                    $userName,
                    $itemUser,
                    $floor
                );
            }
        }
        $em->flush();

        return $userChoices;
    }

    public function downloadFileAction($name)
    {
        $path = $this->pathOrders.$this->dateperiod.'_'.$name.'.xlsx';
        $content = file_get_contents($path);
        $response = new Response();
        $response->headers->set('Content-Type', 'xls/xlsx');
        $response->headers->set('Content-Disposition', 'attachment;filename="'.$this->dateperiod.'_'.$name.'.xlsx');
        $response->setContent($content);

        return $response;
    }

//    public function sendEmailAction()
//    {
//        $message = \Swift_Message::newInstance()
//            ->setSubject('Hello Email')
//            ->setFrom('dm.baryshev@gmail.com')
//            ->setTo('d.baryshev@redmond-rus.com')
//            ->setBody('')
//            ->attach(Swift_Attachment::fromPath($this->pathOrders.$this->dateperiod.'_floor_5_order.xlsx'))
//            ->attach(Swift_Attachment::fromPath($this->pathOrders.$this->dateperiod.'_floor_4_order.xlsx'))
//            ->attach(Swift_Attachment::fromPath($this->pathOrders.$this->dateperiod.'_floor_4_menu.xlsx'))
//            ->attach(Swift_Attachment::fromPath($this->pathOrders.$this->dateperiod.'_floor_5_menu.xlsx'))
//        ;
//        $this->get('mailer')->send($message);
//
//        return $this->render('EnsLunchBundle:Lunch:email.html.twig');
//    }
}