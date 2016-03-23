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

        $scene = null;
        $sceneId = $request->query->get("sceneId");
        
        if ($sceneId){
            $sceneRepo = $this->getDoctrine()->getRepository('AppBundle:Writer\Scene');
            $scene = $sceneRepo->find($sceneId);
        }

        $queryBuilder = $connnectionRepo->createQueryBuilder('e')
            ->orderBy('e.id', 'ASC');

        if ($scene) {
            $queryBuilder->andWhere('e.scene = :scene')
            ->setParameter('scene', $scene);
        }

        $list = $queryBuilder->getQuery()->getResult();

        return $this->render('writer/connectionList.html.twig', ["list"=>$list]);
    }
}
