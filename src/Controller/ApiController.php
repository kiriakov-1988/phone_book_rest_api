<?php

namespace App\Controller;

use App\Entity\PhoneBook;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class ApiController extends Controller
{
    /**
     * @Route("/api", name="api", methods={"GET"})
     */
    public function index()
    {
        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
        ]);
    }



    /**
     * @Route("/api/users", 
     * 			name="list",
     * 			methods={"GET"},
     * 		)
     */
    public function list()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $users = $entityManager->getRepository(PhoneBook::class)->findAll();

        
        if (!$users) {
	        return new JsonResponse(['No users found'], Response::HTTP_NOT_FOUND);
	    }

        $data = $this->get('jms_serializer')->serialize($users, 'json');

        return new JsonResponse(json_decode($data), Response::HTTP_OK);
    }



    /**
     * @Route("/api/users/{id}", 
     * 			name="show",
     * 			methods={"GET"},
     * 			requirements={"id": "\d+"},
     * 		)
     */
    public function show($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(PhoneBook::class)->find($id);

        if (!$user) {
	        return new JsonResponse(['No user found for id' => $id], Response::HTTP_NOT_FOUND);
	    }

	    $data = $this->get('jms_serializer')->serialize($user, 'json');

        return new JsonResponse(json_decode($data), Response::HTTP_OK);
    }



    /**
     * @Route("/api/users/search/{name}", 
     * 			name="search",
     * 			methods={"GET"},
     * 			requirements={"name": "\w+"},
     * 		)
     */
    public function search($name)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $users = $entityManager->getRepository(PhoneBook::class)->findByName($name);

	    if (!$users) {
	        return new JsonResponse(['No user(s) found for name' => $name], Response::HTTP_NOT_FOUND);
	    }

        $data = $this->get('jms_serializer')->serialize($users, 'json');

        return new JsonResponse(json_decode($data), Response::HTTP_OK);
    }



    /**
     * @Route("/api/users", 
     * 			name="add",
     * 			methods={"POST"},
     * 		)
     */
    public function add(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        

        $user = new PhoneBook();

        ///////////////////////////////////////////

        $data = $request->getContent();

	    $user_new = $this->get('jms_serializer')->deserialize($data, PhoneBook::class, 'json');

	    $phone = $user_new->getPhone();
	    $name = $user_new->getName();

        // $name = $request->request->get('name');
        // $phone = $request->request->get('phone');

	    $user->setName($name);
	    $user->setPhone($phone);

	    $entityManager->persist($user);
	    $entityManager->flush();

	    $id = $user->getId();

	    return new JsonResponse(['User created with id' => $id], Response::HTTP_OK);
    }



    /**
     * @Route("/api/users/{id}", 
     * 			name="update",
     * 			methods={"PUT"},
     * 			requirements={"id": "\d+"},
     * 		)
     */
    public function update($id, Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(PhoneBook::class)->find($id);

        if (!$user) {
	        return new JsonResponse(['No user found for id' => $id], Response::HTTP_NOT_FOUND);
	    }

        ///////////////////////////////////////////

	    $data = $request->getContent();

	    $user_new = $this->get('jms_serializer')->deserialize($data, PhoneBook::class, 'json');

	    $phone = $user_new->getPhone();
	    $name = $user_new->getName();

	    // $name = $request->request->get('name');
        // $phone = $request->request->get('phone');

	    $user->setName($name);
	    $user->setPhone($phone);

	    $entityManager->flush();

	    return new JsonResponse(['User updated with id' => $id], Response::HTTP_OK);
    }



    /**
     * @Route("/api/users/{id}", 
     * 			name="delete",
     * 			methods={"DELETE"},
     * 			requirements={"id": "\d+"},
     * 		)
     */
    public function delete($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(PhoneBook::class)->find($id);

        if (!$user) {
	        return new JsonResponse(['No user found for id' => $id], Response::HTTP_NOT_FOUND);
	    }

	    $entityManager->remove($user);
		$entityManager->flush();

		return new JsonResponse(['User deleted with id' => $id], Response::HTTP_OK);

    }


}
