<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CurlController extends Controller
{

    /**
     * @Route("/curl", name="curl")
     */
    public function index(Request $request)
    {
        $search_form = $this->createFormBuilder()
        	->add('name_search', TextType::class, [
        		'label'=> 'api/users/search/',
        		'attr'=> ['value'=> 'name_user'],
        	])
        	->add('search', SubmitType::class, ['attr'=> ['class'=> 'alert-info']] )
        	->getForm();

        $search_form->handleRequest($request);
	    if ($search_form->isSubmitted() && $search_form->isValid()) {
	        
	        $search_name = $search_form->getData();	
	        
	        return $this->redirectToRoute('curl_search', array('name' => $search_name['name_search'] ));

	    } // search-form


	    $post_form = $this->createFormBuilder()
        	->add('new_user', TextareaType::class, [
        		'label'=> 'api/users',
        		'data' => '{"phone":"0123456789","name":"new_name"}'
        	])
        	->add('add', SubmitType::class, ['attr'=> ['class'=> 'alert-success']] )
        	->getForm();

        $post_form->handleRequest($request);
	    if ($post_form->isSubmitted() && $post_form->isValid()) {
	        
	        $new_user = $post_form->getData()['new_user'];
        
	        return $this->redirectToRoute('curl_post', array('data' => $new_user));

	    } // post-form


	    $update_form = $this->createFormBuilder()
	    	->add('id_update', IntegerType::class, [
        		'label'=> 'api/users/',
        		'attr'=> ['value'=> 10]
        	])
        	->add('update_user', TextareaType::class, [
        		'label'=> ' ',
        		'data' => '{"phone":"9876543210","name":"update_name"}'
        	])
        	->add('update', SubmitType::class, ['attr'=> ['class'=> 'alert-warning']] )
        	->getForm();

        $update_form->handleRequest($request);
	    if ($update_form->isSubmitted() && $update_form->isValid()) {
	        
	        $update_user = $update_form->getData()['update_user'];
	        $update_id = $update_form->getData()['id_update'];
        
	        return $this->redirectToRoute('curl_update', 
	        	array(
	        		'id' => $update_id,
	        		'data' => $update_user
	        	)
	        );

	    } // update-form


	    $delete_form = $this->createFormBuilder()
        	->add('id_delete', IntegerType::class, [
        		'label'=> 'api/users/',
        		'attr'=> ['value'=> 10]
        	])
        	->add('delete', SubmitType::class, ['attr'=> ['class'=> 'alert-danger']] )
        	->getForm();

        $delete_form->handleRequest($request);
	    if ($delete_form->isSubmitted() && $delete_form->isValid()) {
	        
	        $delete_id = $delete_form->getData();
 
	        return $this->redirectToRoute('curl_delete', array('id' => $delete_id['id_delete'] ));

	    } // delete-form


        return $this->render('curl/index.html.twig', [
            'controller_name' => 'CurlController',
            'search_form' => $search_form->createView(), 
            'post_form' => $post_form->createView(),
            'update_form' => $update_form->createView(),
            'delete_form' => $delete_form->createView(),
        ]);
    }


    public function curl($url, $method = 'GET', $input = null)
    {
        
        $ch = \curl_init();

        // set method
        \curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        // set url
        \curl_setopt($ch, CURLOPT_URL, $_SERVER['HTTP_HOST'] . "/api/" . $url);

        // \curl_setopt($ch, CURLOPT_HEADER, false);
        \curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

        if ($input) {
        	
        	// set data
        	\curl_setopt($ch, CURLOPT_POSTFIELDS, $input);
        }
        

        //return the transfer as a string
        \curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // $output contains the output string
        $output = \curl_exec($ch);


        // close curl resource to free up system resources
        \curl_close($ch);

        $data = json_decode($output);      

        return $data;
    }

    /**
     * @Route("/curl/users", name="curl_users" )
     */
    public function curl_users()
    {
        
        $data = $this->curl('users');

        return new JsonResponse($data);
    }

    /**
     * @Route("/curl/users/search/{name}", name="curl_search", requirements={"name": "\w+"} )
     */
    public function curl_search($name)
    {
        $data = $this->curl('users/search/' . $name);

        return new JsonResponse($data);
    }


    /**
     * @Route("/curl/post/{data}", name="curl_post" )
     */
    public function curl_post($data)
    {        
        $data = $this->curl('users', 'POST', $data);

        return new JsonResponse($data);
    }

    /**
     * @Route("/curl/update/{id}/{data}", name="curl_update", requirements={"id": "\d+"} )
     */
    public function curl_update($id, $data)
    {        
        $data = $this->curl('users/' . $id, 'PUT', $data);

        return new JsonResponse($data);
    }

    /**
     * @Route("/curl/delete/{id}", name="curl_delete", requirements={"id": "\d+"} )
     */
    public function curl_delete($id)
    {
        $data = $this->curl('users/' . $id, 'DELETE');

        return new JsonResponse($data);
    }
}
