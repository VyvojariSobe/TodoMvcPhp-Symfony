<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ToDo;
use AppBundle\Form\ToDoType;
use AppBundle\Repository\ToDoRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ToDoController.
 *
 * @author Dennis Fridrich <fridrich.dennis@gmail.com>
 */
class ToDoController extends Controller
{
    /**
     * @Route("/{filter}",
     *     name="todos",
     *     requirements={"filter": "all|active|completed"},
     *     defaults={"filter": "all"
     * })
     * @Template()
     * @Method({"GET", "POST"})
     */
    public function indexAction(Request $request, $filter)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:ToDo');

        $newForm = $this->createForm(ToDoType::class);
        $newForm->handleRequest($request);

        if ($newForm->isValid()) {
            $em->persist($newForm->getData());
            $this->addFlash('success', 'Task created');
            $em->flush();

            return $this->redirectToRoute('todos', ['filter' => $filter]);
        }

        $toDos = $repo->getAllToDos($filter);

        $editForms = [];

        foreach ($toDos as $todo) {
            $form = $this->get('form.factory')->createNamed('edit_'.$todo->getId(), ToDoType::class, $todo);
            $form->handleRequest($request);

            if ($form->isValid()) {
                $this->addFlash('success', 'Task updated');
                $em->flush();

                return $this->redirectToRoute('todos', ['filter' => $filter]);
            }

            $editForms[$todo->getId()] = $form->createView();
        }

        return [
            'totos'     => $toDos,
            'completed' => $repo->countCompletedToDos(),
            'active'    => $repo->activeToDosCounter(),
            'filters'   => $repo->getPossibleFilters(),
            'newForm'   => $newForm->createView(),
            'editForms' => $editForms,
        ];
    }

    /**
     * @Route("/{id}/change-status/{status}/{filter}",
     *     name="todos_change_status",
     *     requirements={"status": "(un)?check", "filter": "all|active|completed"},
     *     defaults={"filter": "all"
     * })
     *
     * @param ToDo $toDo
     * @param      $status
     * @param      $filter
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function changeAction(ToDo $toDo, $status, $filter)
    {
        $em = $this->getDoctrine()->getManager();
        $toDo->setIsDone($status == 'check' ? true : false);
        $this->addFlash('success', 'Task status changed');
        $em->flush();

        return $this->redirectToRoute('todos', ['filter' => $filter]);
    }

    /**
     * @Route("/{id}/remove", name="todos_remove")
     *
     * @param ToDo $toDo
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeAction(ToDo $toDo)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($toDo);
        $em->flush();
        $this->addFlash('success', 'Task removed');

        return $this->redirectToRoute('todos');
    }

    /**
     * @Route("/clear-completed", name="todos_clear_completed")
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function clearCompletedAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:ToDo');
        $toDos = $repo->getAllToDos(ToDoRepository::FILTER_COMPLETED);

        foreach ($toDos as $toDo) {
            $em->remove($toDo);
        }
        $em->flush();
        $this->addFlash('success', 'All completed tasks are removed');

        return $this->redirectToRoute('todos');
    }
}
