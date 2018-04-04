<?php

namespace App\Controller;


use App\Entity\PhoneBook;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class WebController extends Controller
{
    /**
     * @Route("/web", name="web_list")
     */
    public function list(Request $request)
    {
        
    	$entityManager = $this->getDoctrine()->getManager();
        $users = $entityManager->getRepository(PhoneBook::class)->findAll();

        $search_user = new PhoneBook();

        $search_form = $this->createFormBuilder($search_user)
        	->add('name', TextType::class)
        	->add('search', SubmitType::class, ['attr'=> ['class'=> 'alert-info']] )
        	->getForm();


        $search_form->handleRequest($request);
	    if ($search_form->isSubmitted() && $search_form->isValid()) {
	        
	        $search_users = $search_form->getData();
	        $users = $entityManager->getRepository(PhoneBook::class)->findByName($search_users->getName());
	    }

        return $this->render('web/list.html.twig', [
            'users' => $users,
            'search_form' => $search_form->createView(),
        ]);
    }

	/**
     * @Route("/web/add", name="web_add")
     */
    public function add(Request $request)
    {
        
    	$entityManager = $this->getDoctrine()->getManager();

        $new_user = new PhoneBook();

        $add_form = $this->createFormBuilder($new_user)
        	->add('name', TextType::class)
        	->add('phone', TextType::class)
        	->add('add', SubmitType::class)
        	->getForm();


	    $add_form->handleRequest($request);

	    if ($add_form->isSubmitted() && $add_form->isValid()) {
	        
	        $new_user = $add_form->getData();
	        
	        $entityManager->persist($new_user);
	    	$entityManager->flush();

	    	return $this->redirectToRoute('web_list');
	    }

        return $this->render('web/add.html.twig', [
            'add_form' => $add_form->createView(),
            'title' => 'Add new contact'
        ]);
    }

    /**
     * @Route("/web/update/{id}", name="web_update", requirements={"id": "\d+"})
     */
    public function update($id, Request $request)
    {
        
    	$entityManager = $this->getDoctrine()->getManager();

    	$user = $entityManager->getRepository(PhoneBook::class)->find($id);

    	if (!$user) {
	        return $this->redirectToRoute('web_add');
	    }

        $add_form = $this->createFormBuilder($user)
        	->add('name', TextType::class)
        	->add('phone', TextType::class)
        	->add('update', SubmitType::class)
        	->getForm();


	    $add_form->handleRequest($request);

	    if ($add_form->isSubmitted() && $add_form->isValid()) {
	        
	        $user = $add_form->getData();
	        
	        $entityManager->persist($user);
	    	$entityManager->flush();

	    	return $this->redirectToRoute('web_list');
	    }

        return $this->render('web/add.html.twig', [
            'add_form' => $add_form->createView(),
            'title' => 'Update contact (' . $id . ')' 
        ]);
    }

    /**
     * @Route("/web/delete/{id}", name="web_delete", requirements={"id": "\d+"})
     */
    public function delete($id)
    {
        
    	$entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(PhoneBook::class)->find($id);

        if ($user) {
	        $entityManager->remove($user);
			$entityManager->flush();
	    }

        return $this->redirectToRoute('web_list');
    }
}
