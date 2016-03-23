<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SceneListController extends BaseController
{
    /**
     * @Route("/scene/list", name="sceneList")
     */
    public function indexAction(Request $request)
    {
        $project = $this->entityFromSession($request, 'currentProject');
        $sceneRepo = $this->getDoctrine()->getRepository('AppBundle:Writer\Scene');

        $query = $sceneRepo->createQueryBuilder('s')
            ->where('s.project = :project')
            ->setParameter('project', $project)
            ->orderBy('s.id', 'ASC')
            ->getQuery();

        $list = $query->getResult();

        return $this->render('writer/sceneList.html.twig', ["list"=>$list]);
    }
}
