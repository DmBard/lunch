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

    public function adminIndexAction()
    {
        /** @var ManagerRegistry $em */
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('EnsLunchBundle:Lunch');

        $entities = $repo->getActiveLunches();
        $user = $this->get('security.token_storage')->getToken()->getUser();

        return $this->render(
            'EnsLunchBundle:Lunch:admin_index.html.twig',
            array(
                'entities' => $entities,
                'days' => $this->days,
                'categories' => $this->categories,
                'user' => $user,
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

        $request = $this->get('request');
        $randomMode = $request->request->get('random_mode');

        //Add data into jointable
        if (!$randomMode) {
            $this->addDataInJointable($user);
        } else {
            $this->addRandomDataInJointable($user);
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
            )
        );
    }

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

    public function uploadAction(Request $request)
    {
        $document = new Document();
        $form = $this->createFormBuilder($document)
            ->add('name')
            ->add('file')
            ->add('submit', 'submit', array('label' => 'Create'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $document->upload();

            $em->persist($document);
            $em->flush();

            $xlsWriter = $this->get('ens_lunch.xls_manager');
            $xlsWriter->parseXlsFile();

            return $this->redirectToRoute('ens_lunch_show_all');
        }

        return $this->render(
            'EnsLunchBundle:Lunch:new.html.twig',
            array(
                'form' => $form->createView(),
            )
        );
    }

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

    /**
     * Finds and displays a Lunch entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EnsLunchBundle:Lunch')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Lunch entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render(
            'EnsLunchBundle:Lunch:show.html.twig',
            array(
                'entity' => $entity,
                'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing Lunch entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EnsLunchBundle:Lunch')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Lunch entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render(
            'EnsLunchBundle:Lunch:edit.html.twig',
            array(
                'entity' => $entity,
                'edit_form' => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Edits an existing Lunch entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EnsLunchBundle:Lunch')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Lunch entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('ens_lunch_edit', array('id' => $id)));
        }

        return $this->render(
            'EnsLunchBundle:Lunch:edit.html.twig',
            array(
                'entity' => $entity,
                'edit_form' => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Deletes a Lunch entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('EnsLunchBundle:Lunch')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Lunch entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('ens_lunch'));
    }

    public function generateFilesAction()
    {
        $floorList = [
            'floor_4',
            'floor_5',
        ];

        $users = $this->getDoctrine()->getManager()->getRepository('EnsLunchBundle:User')->findAll();
        foreach ($floorList as $floor) {
//array of user choices
            $userChoices = [];
            $names = [];
            foreach ($users as $itemUser) {
                $joinList = $this->getDoctrine()->getManager()->getRepository(
                    'EnsLunchBundle:Jointable'
                )->getActiveJoinsByOneUserAndFloor($itemUser, $floor);

                if ($joinList) {
                    $lunchList = [];
                    foreach ($joinList as $joinItem) {
                        array_push(
                            $lunchList,
                            $this->getDoctrine()->getManager()->getRepository(
                                'EnsLunchBundle:Lunch'
                            )->findOneBy(array('id' => $joinItem->getIdLunch(), 'active' => 1))
                        );
                    }

                    //lunch list is empty, because user didn't do the order, then default mode is on
                    //default mode (action) - delete user order
                    if (($lunchList[0] == null) and ($itemUser->getDefaultAction() == 1)) {
                        continue;
                    }

                    //lunch list is empty, because user didn't do the order, then default mode is on
                    //default mode (action) - random choice
                    elseif (($lunchList[0] == null) and ($itemUser->getDefaultAction() == 2)) {
                        $userChoices = $this->setRandomChoice(
                            $itemUser,
                            $floor
                        );
                    }
                    //lunch list is empty, because user didn't do the order, then default mode is on
                    //default mode (action) - previous choice
                    elseif (($lunchList[0] == null) and ($itemUser->getDefaultAction() == 3)) {
                        $userChoices = $this->setPreviousChoice(
                            $joinList,
                            $itemUser,
                            $floor
                        );

                    } else {
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
                    array_push($names, $joinList[0]->getName());
                }
            }

            $xlsWriter = $this->get('ens_lunch.xls_manager');
            $xlsWriter->writeOrderXlsFile($names, $userChoices, $floor);
            $xlsWriter->writeMenuXlsFile($floor);
        }

        return $this->render(
            'EnsLunchBundle:Lunch:order_files.html.twig'
        );
    }

    /**
     * @param $user
     */
    private function addDataInJointable($user)
    {
        $em = $this->getDoctrine()->getManager();
        $userName = $user->getUserName();

        $pastJoins = $em->getRepository('EnsLunchBundle:Jointable')->getPastUserJoins($userName);

        foreach ($pastJoins as $item) {
            $item->setActive(0);
            $em->persist($item);
        }
        $em->flush();

        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');

        //insert a user choice in the JoinTable
        foreach ($this->categories as $category) {
            foreach ($this->days as $day) {
                $join = new Jointable();
                $floor = $request->request->get('floor');
                $idLunch = $_POST[$category][$day];
                $join->setIdLunch($idLunch);
                $join->setUserName($userName);
                $join->setName($user->getName());
                $join->setFloor($floor);
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
        $em->flush();

        $em = $this->getDoctrine()->getManager();

        //insert a user choice in the JoinTable
        foreach ($this->categories as $category) {
            foreach ($this->days as $day) {
                $joinTable = new Jointable();
                $lunches = $em->getRepository('EnsLunchBundle:Lunch')->getActiveCategoryAndDayLunches($category, $day);
                $idLunch = $lunches[rand(0, count($lunches) - 1)]->getId();
                $floor = $_POST['floor'];
                $joinTable->setIdLunch($idLunch);
                $joinTable->setUserName($userName);
                $joinTable->setName($user->getName());
                $joinTable->setFloor($floor);
                $joinTable->setActive(1);
                $em->persist($joinTable);
            }
        }

        $em->flush();
    }

    /**
     * @param $em
     * @param $userName
     * @return array
     */
    public function clearPastJoins($em, $userName)
    {
        $pastJoins = $em->getRepository('EnsLunchBundle:Jointable')->getPastUserJoins($userName);

        foreach ($pastJoins as $item) {
            $item->setActive(0);
            $em->persist($item);
        }

        return array($pastJoins, $item);
    }

    /**
     * @param $em
     * @param $itemDay
     * @param $itemCategory
     * @param $itemLunch
     * @param $userName
     * @param $itemUser
     * @param $floor
     * @return array
     */
    public function createNewJoins($em, $itemDay, $itemCategory, $itemLunch, $userName, $itemUser, $floor)
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

        return array($join, $lunch);
    }

    /**
     * @param $joinList
     * @param $itemUser
     * @param $floor
     * @return array
     */
    public function setPreviousChoice($joinList, $itemUser, $floor)
    {
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
    }

    /**
     * @param $itemUser
     * @param $floor
     * @return array
     */
    public function setRandomChoice($itemUser, $floor)
    {
        $userChoices = [];
        $em = $this->getDoctrine()->getManager();

        $userName = $itemUser->getUserName();

        $this->clearPastJoins($em, $userName);

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

        return  $userChoices;
    }


    /**
     * Creates a form to create a Lunch entity.
     *
     * @param Lunch $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Lunch $entity)
    {
        $form = $this->createForm(
            new LunchType(),
            $entity,
            array(
                'action' => $this->generateUrl('ens_lunch_create'),
                'method' => 'POST',
            )
        );

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Creates a form to edit a Lunch entity.
     *
     * @param Lunch $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Lunch $entity)
    {
        $form = $this->createForm(
            new LunchType(),
            $entity,
            array(
                'action' => $this->generateUrl('ens_lunch_update', array('id' => $entity->getId())),
                'method' => 'PUT',
            )
        );

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Creates a form to delete a Lunch entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('ens_lunch_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }

    public function downloadFile4FloorMenuAction()
    {
        return $this->downloadFile('floor_4_menu');
    }

    public function downloadFile5FloorMenuAction()
    {
        return $this->downloadFile('floor_5_menu');
    }

    public function downloadFile4FloorOrderAction()
    {
        return $this->downloadFile('floor_4_order');
    }

    public function downloadFile5FloorOrderAction()
    {
        return $this->downloadFile('floor_5_order');
    }

    private function downloadFile($name)
    {
        $path = __DIR__.'/../../../../web/uploads/orders/'.$this->dateperiod.'_'.$name.'.xlsx';
        $content = file_get_contents($path);
        $response = new Response();
        $response->headers->set('Content-Type', 'xls/xlsx');
        $response->headers->set('Content-Disposition', 'attachment;filename="'.$this->dateperiod.'_'.$name.'.xlsx');
        $response->setContent($content);

        return $response;
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
            )
        );
    }
}