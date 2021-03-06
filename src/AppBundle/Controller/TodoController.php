<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Todo;
// use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType as TypeChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType as TypeDateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType as TypeTextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType as TypeTextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class TodoController extends Controller
{
    /**
     * @Route("/", name="todo_list")
     */
    public function listAction()
    {
        $todos = $this->getDoctrine()->getRepository('AppBundle:Todo')->findAll();
        return $this->render('todo/index.html.twig', array(
            'todos' => $todos
        ));
    }

    /**
     * @Route("/todo/create", name="todo_create")
     */
    public function createAction(Request $request)
    {
        $todo = new Todo;

        $form = $this->createFormBuilder($todo)
                ->add('name', TypeTextType::class, array('attr' => array('class' => 'form-control mb-3')))
                ->add('category', TypeTextType::class, array('attr' => array('class' => 'form-control mb-3')))
                ->add('description', TypeTextareaType::class, array('attr' => array('class' => 'form-control mb-3')))
                ->add('priority', TypeChoiceType::class, 
                array('choices' => array('Low' => 'Low', 'Normal' => 'Normal', 'High' => 'High'),
                'attr' => array('class' => 'form-control mb-3')))
                ->add('due_date', TypeDateTimeType::class, array('attr' => array('class' => 'formcontrol mb-3')))
                ->add('save', SubmitType::class, array('label' => 'Create Todo', 'attr' => array('class' => 'btn btn-primary')))
                ->getForm();
                
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //Get Data
            $name = $form['name']->getData();
            $category = $form['category']->getData();
            $description = $form['description']->getData();
            $priority = $form['priority']->getData();
            $due_date = $form['due_date']->getData();

            $now = new \DateTime('now');

            $todo->setName($name);
            $todo->setCategory($category);
            $todo->setDescription($description);
            $todo->setPriority($priority);
            $todo->setDueDate($due_date);
            $todo->setCreateDate($now);

            $em = $this->getDoctrine()->getManager();
            $em->persist($todo);
            $em->flush();

            $this->addFlash(
                'notice',
                'Todo added'
            );
            return $this->redirectToRoute('todo_list');
        }
        
        return $this->render('todo/create.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/todo/edit/{id}", name="todo_edit")
     */
    public function editAction($id, Request $request)
    {
        $todo = $this->getDoctrine()
            ->getRepository('AppBundle:Todo')
            ->find($id);

            $now = new \DateTime('now');

            $todo->setName($todo->getName());
            $todo->setCategory($todo->getCategory());
            $todo->setDescription($todo->getDescription());
            $todo->setPriority($todo->getPriority());
            $todo->setDueDate($todo->getDueDate());
            $todo->setCreateDate($now);

        $form = $this->createFormBuilder($todo)
                ->add('name', TypeTextType::class, array('attr' => array('class' => 'form-control mb-3')))
                ->add('category', TypeTextType::class, array('attr' => array('class' => 'form-control mb-3')))
                ->add('description', TypeTextareaType::class, array('attr' => array('class' => 'form-control mb-3')))
                ->add('priority', TypeChoiceType::class, 
                array('choices' => array('Low' => 'Low', 'Normal' => 'Normal', 'High' => 'High'),
                'attr' => array('class' => 'form-control mb-3')))
                ->add('due_date', TypeDateTimeType::class, array('attr' => array('class' => 'formcontrol mb-3')))
                ->add('save', SubmitType::class, array('label' => 'Create Todo', 'attr' => array('class' => 'btn btn-primary')))
                ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //Get Data
            $name = $form['name']->getData();
            $category = $form['category']->getData();
            $description = $form['description']->getData();
            $priority = $form['priority']->getData();
            $due_date = $form['due_date']->getData();

            $now = new \DateTime('now');

            $em = $this->getDoctrine()->getManager();
            $todo = $em->getRepository('AppBundle:Todo')->find($id);

            $todo->setName($name);
            $todo->setCategory($category);
            $todo->setDescription($description);
            $todo->setPriority($priority);
            $todo->setDueDate($due_date);
            $todo->setCreateDate($now);

            $em->flush();

            $this->addFlash(
                'notice',
                'Todo updated'
            );
            return $this->redirectToRoute('todo_list');
        }
        
        return $this->render('todo/edit.html.twig', array(
            'todo' => $todo,
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/todo/details/{id}", name="todo_details")
     */
    public function detailsAction($id)
    {
        $todo = $this->getDoctrine()
                ->getRepository('AppBundle:Todo')
                ->find($id);
        
        return $this->render('todo/details.html.twig', array(
            'todo' => $todo
        ));
    }
}
