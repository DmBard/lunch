<?php

namespace Ens\LunchBundle\Controller;

use Ens\LunchBundle\Entity\Document;
use Ens\LunchBundle\Entity\Jointable;
use Ens\LunchBundle\Entity\Lunch;
use Ens\LunchBundle\Form\LunchType;
use PHPExcel_IOFactory;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


/**
 * Lunch controller.
 *
 */
class LunchController extends Controller
{

    /**
     * Lists all Lunch entities.
     *
     */
    public function indexAction()
    {
        /** @var ManagerRegistry $em */
        $em = $this->getDoctrine()->getManager();
        $repoLunch = $em->getRepository('EnsLunchBundle:Lunch');

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

        $entities = $repoLunch->getActiveLunches();
        $user = $this->get('security.token_storage')->getToken()->getUser();


        return $this->render(
            'EnsLunchBundle:Lunch:index.html.twig',
            array(
                'entities' => $entities,
                'days' => $days,
                'categories' => $categories,
                'user' => $user
            )
        );
    }

    public function adminIndexAction()
    {
        /** @var ManagerRegistry $em */
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('EnsLunchBundle:Lunch');

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

        $entities = $repo->getActiveLunches();
        $user = $this->get('security.token_storage')->getToken()->getUser();


        return $this->render(
            'EnsLunchBundle:Lunch:admin_index.html.twig',
            array(
                'entities' => $entities,
                'days' => $days,
                'categories' => $categories,
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

    /**
     * Creates a new Lunch entity.
     *
     */
//    public function createAction(Request $request)
//    {
//        $entity = new Document();
//        $form = $this->createCreateForm($entity);
//        $form->handleRequest($request);
//
//        if ($form->isValid()) {
//            $em = $this->getDoctrine()->getManager();
//            $entity->file->move(
//                '/home/bard/PhpstormProjects/lunch/web/uploads/file',
//                $entity->file->getClientOriginalName()
//            );
//            $em->persist($entity);
//            $em->flush();
//
//
//            $em = $this->getDoctrine()->getManager();
//            $repo = $em->getRepository('EnsLunchBundle:Lunch');
//            $entities = $repo->findAll();
//
//            return $this->redirect($this->generateUrl('ens_lunch_show_all', array('entities' => $entities)));
//        }
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
     * Displays a form to create a new Lunch entity.
     *
     */
    public function newAction()
    {
        $entity = new Lunch();
        $form = $this->createCreateForm($entity);

        return $this->render(
            'EnsLunchBundle:Lunch:new.html.twig',
            array(
                'entity' => $entity,
                'form' => $form->createView(),
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

    /**
     * Displays a order form
     *
     */
    public function orderAction()
    {

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

        $em = $this->getDoctrine()->getManager();

        $user = $this->get('security.token_storage')->getToken()->getUser();
        if (!$em->contains($user)) {
            $em->persist($user);
        }

        $idUser = $user->getId();

        $pastJoins = $em->getRepository('EnsLunchBundle:Jointable')->getPastUserJoins($idUser);

        foreach ($pastJoins as $item) {
            $item->setActive(0);
            $em->persist($item);
        }
        $em->flush();

        $em = $this->getDoctrine()->getManager();

        //insert a user choice in the JoinTable
        foreach ($categories as $category) {
            foreach ($days as $day) {
                $joinTable = new Jointable();
                $idLunch = $_POST[$category][$day];
                $joinTable->setIdLunch($idLunch);
                $joinTable->setIdUser($idUser);
                $joinTable->setActive(1);
                $em->persist($joinTable);
            }
        }

        $em->flush();

        $users = $em = $this->getDoctrine()->getManager()->getRepository('EnsLunchBundle:User')->findAll();
        $joins = $em = $this->getDoctrine()->getManager()->getRepository('EnsLunchBundle:Jointable')->getActiveJoinsOrderedByUsers();
        $lunches = $em = $this->getDoctrine()->getManager()->getRepository('EnsLunchBundle:Lunch')->getActiveLunches();


        return $this->render(
            'EnsLunchBundle:Lunch:order.html.twig',
            array(
                'days' => $days,
                'categories' => $categories,
                'lunches' => $lunches,
                'users' => $users,
                'joins' => $joins,
            )
        );
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
     * @return mixed
     * @throws \PHPExcel_Reader_Exception
     */
    public function parseXlsFile()
    {
        $inputFileName = '/web/sites/lunch.local/web/uploads/documents/'.date('m-d-Y').'menu.xls';

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
                    //== display each cell value
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
}
