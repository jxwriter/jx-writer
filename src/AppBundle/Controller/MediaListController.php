<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class MediaListController extends BaseController
{
    /**
     * @Route("/media/list", name="mediaList")
     */
    public function indexAction(Request $request)
    {
        
        $scene = null;
        $sceneId = $request->query->get("sceneId");
        
        if ($sceneId){
            $sceneRepo = $this->getDoctrine()->getRepository('AppBundle:Writer\Scene');
            $scene = $sceneRepo->find($sceneId);
        }

        $project = $this->entityFromSession($request, 'currentProject');
        $repo = $this->getDoctrine()->getRepository('AppBundle:Writer\Media');

        $list = $repo->getMediaListQueryBuilder($project, $scene)->getQuery()->getResult();

        return $this->render('writer/mediaList.html.twig', ["list"=>$list]);
    }
}
