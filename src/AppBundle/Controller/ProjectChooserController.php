<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ProjectChooserController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
		$projectRepo = $this->getDoctrine()->getRepository('AppBundle:Writer\Project');

		if ($request->query->has("projectId")) {
			//add to session
			//redirect to list
		}

    	$projects = $projectRepo->findAll();
        return $this->render('writer/projectChooser.html.twig', 
        	array("projects"=>$projects));
    }
}
