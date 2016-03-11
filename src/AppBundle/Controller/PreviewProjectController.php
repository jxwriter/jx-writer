<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\Writer\Media;
use AppBundle\Entity\Writer\Scene;
use AppBundle\Entity\Writer\SceneConnection;
use AppBundle\Entity\Writer\Product;

class PreviewProjectController extends Controller
{
    /**
     * @Route("/project/preview/{projectId}/{sceneId}", name="previewProject")
     */
    public function indexAction(Request $request, $projectId, $sceneId)
    {
        $projectRepo = $this->getDoctrine()->getRepository('AppBundle:Writer\Project');
        $sceneRepo = $this->getDoctrine()->getRepository('AppBundle:Writer\Scene');

        $project = $projectRepo->find($projectId);
        $scene = $sceneRepo->find($sceneId);


        /*
        $query = $sceneRepo->createQueryBuilder('s')
            ->where('s.project = :project')
            ->setParameter('project', $project)
            ->getQuery();

        $result = $query->getResult();
        print_r($result);*/

        return $this->render('writer/previewProject.html.twig', 
            array("project"=>$project, "scene" => $scene));
    }
}
