<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ConnectionListController extends BaseController
{
    /**
     * @Route("/connection/list", name="connectionList")
     */
    public function indexAction(Request $request)
    {
        $project = $this->entityFromSession($request, 'currentProject');
        $connnectionRepo = $this->getDoctrine()->getRepository('AppBundle:Writer\SceneConnection');
        $sceneRepo = $this->getDoctrine()->getRepository('AppBundle:Writer\Scene');
            
        $parentScene = null;
        $childScene = null;

        $parentSceneId = $request->query->get("parentSceneId");
        $childSceneId = $request->query->get("childSceneId");
        
        if ($parentSceneId){
            $parentScene = $sceneRepo->find($parentSceneId);
        }

        if ($childSceneId){
            $childScene = $sceneRepo->find($childSceneId);
        }

        $queryBuilder = $connnectionRepo->getConnectionListQueryBuilder($project, $parentScene, $childScene);
        $list = $queryBuilder->getQuery()->getResult();

        return $this->render('writer/connectionList.html.twig', ["list"=>$list]);
    }
}
