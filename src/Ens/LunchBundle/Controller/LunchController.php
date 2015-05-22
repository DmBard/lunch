<?php

namespace Ens\LunchBundle\Controller;

use Ens\LunchBundle\Entity\Lunch;
use Ens\LunchBundle\Form\LunchType;
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
        $repo = $em->getRepository('EnsLunchBundle:Lunch');

//        /** @var string[] $categories */
//        $categories = [
//            'Salad',
//            'Soup',
//            'Main Course',
//            'Dessert',
//        ];
//
//        /** @var string[] $days */
//        $days = [
//            'Monday',
//            'Tuesday',
//            'Wednesday',
//            'Thursday',
//            'Friday',
//        ];
//
//        $entities = [];
//        foreach ($categories as $category) {
//            foreach ($days as $day) {
//                $entity = $repo->getCategoryDayLunches($day, $category);
//
//                if ($entity != null) {
//                    foreach ($entity as $item) {
//                        array_push($entities, $item);
//                    }
//                }
//            }
//        }

        $entities = $repo->findAll();

        return $this->render(
            'EnsLunchBundle:Lunch:index.html.twig',
            array(
                'entities' => $entities
            )
        );
    }

    /**
     * Creates a new Lunch entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Lunch();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('ens_lunch_show', array('id' => $entity->getId())));
        }

        return $this->render(
            'EnsLunchBundle:Lunch:new.html.twig',
            array(
                'entity' => $entity,
                'form' => $form->createView(),
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
}
