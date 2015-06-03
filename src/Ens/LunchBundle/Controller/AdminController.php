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
use Ens\LunchBundle\Entity\Order;
use PHPExcel_IOFactory;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller
{

    protected $categories;
    protected $days;

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

            $this->parseXlsFile();

            return $this->redirectToRoute('ens_lunch_show_all');
        }

        return $this->render(
            'EnsLunchBundle:Lunch:new.html.twig',
            array(
                'form' => $form->createView(),
            )
        );
    }

    public function uploadOrderAction(Request $request)
    {
        $order = new Order();
        $form = $this->createFormBuilder($order)
            ->add('name')
            ->add('file')
            ->add('submit', 'submit', array('label' => 'Create'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $order->upload();

            $em->persist($order);
            $em->flush();

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

    public function showAllDocsAction()
    {
        /** @var ManagerRegistry $em */
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('EnsLunchBundle:Document');

        $entities = $repo->findAll();

        return $this->render(
            'EnsLunchBundle:Lunch:all_doc.html.twig',
            array(
                'entities' => $entities
            )
        );
    }

//    /**
//     * Displays a form to create a new Lunch entity.
//     *
//     */
//    public function newAction()
//    {
//        $entity = new Lunch();
//        $form = $this->createCreateForm($entity);
//
//        return $this->render(
//            'EnsLunchBundle:Lunch:new.html.twig',
//            array(
//                'entity' => $entity,
//                'form' => $form->createView(),
//            )
//        );
//    }

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

    public function formArrayOfUserChoices()
    {
        $users = $this->getDoctrine()->getManager()->getRepository('EnsLunchBundle:User')->findAll();

//array of user choices
        $userChoices = [];
        $names = [];
        foreach ($users as $itemUser) {
            $joinList = $this->getDoctrine()->getManager()->getRepository(
                'EnsLunchBundle:Jointable'
            )->getActiveJoinsByOneUser($itemUser);

            if ($joinList) {
                array_push($names, $joinList[0]->getName());
                $lunchList = [];
                foreach ($joinList as $joinItem) {
                    array_push(
                        $lunchList,
                        $this->getDoctrine()->getManager()->getRepository('EnsLunchBundle:Lunch')->findOneBy(
                            array('id' => $joinItem->getIdLunch())
                        )
                    );
                }

                foreach ($this->categories as $itemCategory) {
                    foreach ($this->days as $itemDay) {
                        foreach ($lunchList as $itemLunch) {
                            if (($itemLunch->getCategories() == $itemCategory) and ($itemLunch->getDay() == $itemDay)) {
                                array_push($userChoices, $itemLunch->getCount());
                            }
                        }
                    }
                }
            }
        }

        $this->writeOrderXlsFile($names, $userChoices);
        $this->writeMenuXlsFile();
    }

    /**
     * @param $user
     */
    public function addDataInJointable($user)
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
                $idLunch = $_POST[$category][$day];
                $joinTable->setIdLunch($idLunch);
                $joinTable->setUserName($userName);
                $joinTable->setName($user->getName());
                $joinTable->setActive(1);
                $em->persist($joinTable);
            }
        }

        $em->flush();
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

    /**
     * Displays a order form
     *
     */
    public function orderAction()
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $this->addDataInJointable($user);

        $joins = $this->getDoctrine()->getManager()->getRepository(
            'EnsLunchBundle:Jointable'
        )->getActiveJoinsByOneUser($user);
        $lunches = $this->getDoctrine()->getManager()->getRepository('EnsLunchBundle:Lunch')->getActiveLunches();


        $this->formArrayOfUserChoices();

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

    public function downloadMenuFileAction()
    {
//        $path = $this->get('kernel')->getRootDir(). "/reports/" . $filename;
        $path = __DIR__.'/../../../../web/uploads/orders/'.date('d-m-Y').'menu.xlsx';
        $content = file_get_contents($path);

        $response = new Response();

        $response->headers->set('Content-Type', 'xls/xlsx');
        $response->headers->set('Content-Disposition', 'attachment;filename="'.date('d-m-Y').'menu.xlsx');

        $response->setContent($content);
        return $response;
    }

    public function downloadOrderFileAction()
    {
//        $path = $this->get('kernel')->getRootDir(). "/reports/" . $filename;
        $path = __DIR__.'/../../../../web/uploads/orders/'.date('d-m-Y').'order.xlsx';
        $content = file_get_contents($path);

        $response = new Response();

        $response->headers->set('Content-Type', 'xls/xlsx');
        $response->headers->set('Content-Disposition', 'attachment;filename="'.date('d-m-Y').'order.xlsx');

        $response->setContent($content);
        return $response;
    }

    /**
     * @return mixed
     * @throws \PHPExcel_Reader_Exception
     */
    public function parseXlsFile()
    {
        $inputFileName = __DIR__.'/../../../../web/uploads/documents/'.date('d-m-Y').'menu.xlsx';

//  Read your Excel workbook
        try {
            $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($inputFileName);
        } catch (Exception $e) {
            die('Error loading file "'.pathinfo($inputFileName, PATHINFO_BASENAME).'": '.$e->getMessage());
        }

//  Get worksheet dimensions
        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $arrayLabel = array('B', 'D', 'F', 'H', 'J');

        /** @var string[] $categories */
        $categories = [
            'Salad',
            'Main_Course',
            'Soup',
            'Dessert',
        ];

        /** @var string[] $days */
        $days = [
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
        ];

        /** @var integer $num_category */
        $num_category = 0;

        /** @var integer $num */
        $num = 0;

        //The last lunches are not active
        $em = $this->getDoctrine()->getManager();
        $lunches = $em->getRepository('EnsLunchBundle:Lunch')->getActiveLunches();

        foreach ($lunches as $item) {
            $item->setActive(0);
            $em->persist($item);
        }
        $em->flush();

//  Loop through each row of the worksheet in turn
        for ($row = 2; $row <= $highestRow; $row++) {
            $num++;
            if ($sheet->getCell('B'.$row)->getValue() != '') {
                for ($column = 0; $column < count($arrayLabel); $column++) {
                    $description = $sheet->getCell($arrayLabel[$column].$row)->getValue();
                    $em = $this->getDoctrine()->getManager();
                    $lunch = new Lunch();
// display each cell value
                    $lunch->setCategories($categories[$num_category]);
                    $lunch->setDay($days[$column]);
                    $lunch->setCount($num);
                    $lunch->setActive(1);
                    $lunch->setDescription($description);
                    $em->persist($lunch);
                    $em->flush();
                }
            } else {
                $num_category++;
                $num = 0;
            }
            if ($num_category == 4) {
                break;
            }
        }
    }

    /**
     * @return mixed
     * @throws \PHPExcel_Reader_Exception
     */
    public function writeOrderXlsFile($names, $userChoices)
    {
        $inputFileName = __DIR__.'/../../../../web/uploads/orders/form_order.xlsx';

//  Read your Excel workbook
        try {
            $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($inputFileName);
        } catch (Exception $e) {
            die('Error loading file "'.pathinfo($inputFileName, PATHINFO_BASENAME).'": '.$e->getMessage());
        }

//  Get worksheet dimensions
        $sheet = $objPHPExcel->setActiveSheetIndex(0);
        $firstRow = 2;
        $countOfDishes = 4;
        $highestRow = $firstRow + $countOfDishes * count($names) - 1;
        $arrayLabel = array('B', 'C', 'D', 'E', 'F', 'G');

        /** @var string[] $categoriesRus */
        $categoriesRus = [
            'Салат',
            'Горячее',
            'Суп',
            'Дессерт',
        ];

        /** @var string[] $categoriesRus */
        $daysRus = [
            'Пн.',
            'Вт.',
            'Ср.',
            'Чт.',
            'Пт.',
        ];

// Change the file
        $numName = 0;
        $numCategory = 0;
        $numChoice = 0;
        $numLabel = 1;

        //insert name
        foreach ($names as $name) {
            $sheet->setCellValue('A'.($firstRow + $numName * $countOfDishes), 'ФИО');
            $partsOfName = explode(" ", $name);
            $sheet->setCellValue('A'.($firstRow + $numName * $countOfDishes + 1), $partsOfName[0]);
            $sheet->setCellValue('A'.($firstRow + $numName * $countOfDishes + 2), $partsOfName[1]);
            $sheet->setCellValue('A'.($firstRow + $numName * $countOfDishes + 3), $partsOfName[2]);
            $numName++;
        }

        //insert days of week
        foreach ($daysRus as $day) {
            $sheet->setCellValue($arrayLabel[$numLabel].'1', $day);
            $numLabel++;
        }

        //insert user choices and categories of dishes
        for ($row = $firstRow; $row <= $highestRow; $row++) {
            foreach ($arrayLabel as $column) {
                if ($column == 'B') {
                    $sheet->setCellValue($column.$row, $categoriesRus[$numCategory]);
                    $numCategory++;
                    if ($numCategory == $countOfDishes) {
                        $numCategory = 0;
                    }
                } else {
                    $sheet->setCellValue($column.$row, $userChoices[$numChoice]);
                    $numChoice++;
                }
            }
        }

// Write the file
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $inputFileType);
        $objWriter->save(__DIR__.'/../../../../web/uploads/orders/'.date('d-m-Y').'order.xlsx');
    }

    public function writeMenuXlsFile()
    {
        $inputFileName = __DIR__.'/../../../../web/uploads/documents/'.date('d-m-Y').'menu.xlsx';

//  Read your Excel workbook
        try {
            $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($inputFileName);
        } catch (Exception $e) {
            die('Error loading file "'.pathinfo($inputFileName, PATHINFO_BASENAME).'": '.$e->getMessage());
        }

//  Get worksheet dimensions
        $firstRow = 2;
        $sheet = $objPHPExcel->setActiveSheetIndex(0);
        $highestRow = $sheet->getHighestRow();
        $arrayLabel = array('C', 'E', 'G', 'I', 'K');

        //get the number of servings of each dish
        $numServings = [];
        $lunches = $this->getDoctrine()->getManager()->getRepository('EnsLunchBundle:Lunch')->getActiveLunches();
        foreach ($lunches as $lunch) {
            $countOneLunch = count(
                $this->getDoctrine()->getManager()->getRepository('EnsLunchBundle:Jointable')->getActiveJoinsByOneLunch(
                    $lunch
                )
            );
            array_push($numServings, $countOneLunch);
        }

// Change the file
        $numLunch = 0;

        //insert user choices and categories of dishes
        for ($row = $firstRow; $row <= $highestRow; $row++) {
            foreach ($arrayLabel as $column) {
                if ($sheet->getCell('A'.$row)->getValue() != '') {
                    $sheet->setCellValue($column.$row, $numServings[$numLunch]);
                    $numLunch++;
                }
            }
        }

// Write the file
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $inputFileType);
        $objWriter->save(__DIR__.'/../../../../web/uploads/orders/'.date('d-m-Y').'menu.xlsx');
    }
}