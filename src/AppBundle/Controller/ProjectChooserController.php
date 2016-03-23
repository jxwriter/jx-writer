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

    	$projects = $projectRepo->findAll();
        return $this->render('writer/projectChooser.html.twig', 
        	array("projects"=>$projects));
    }

    /**
     * @Route("/{projectId}", name="chooseProject")
     */
    public function chooseProjectAction(Request $request, $projectId)
    {
        $projectRepo = $this->getDoctrine()->getRepository('AppBundle:Writer\Project');

        $project =  $projectRepo->findById($projectId);        
        $projects = $projectRepo->findAll();

        $session = $request->getSession();

        $session->set('currentProject', $project[0]);

        return $this->render('writer/projectChooser.html.twig', 
            array("projects"=>$projects));
    }

}
