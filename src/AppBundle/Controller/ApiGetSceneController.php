<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\JsonResponse;

class ApiGetSceneController extends Controller
{
    /**
     * @Route("/api/scene/{sceneId}", name="apiGetScene")
     */
    public function indexAction(Request $request, $sceneId)
    {
    	$sceneRepo = $this->getDoctrine()->getRepository('AppBundle:Writer\Scene');
        
        $scene = $sceneRepo->find($sceneId);
        $project = $scene->getProject();

        $result = array(
        	"id" => $scene->getId(),
        	"title" => $scene->getId(),
        	"actions" => $scene->getActions(),
        	"conditions" => $scene->getConditions(),
        	"medias" => array(),
        	"connections" => array(),
        );

        $medias = $scene->getMedias();

        foreach ($medias as $media) {
        	$m = array(
        		"format" => $media->getFormat(),
        		"content" => $media->getContent(),
        		"conditions" => $media->getConditions(),
        	);

        	$result["medias"][] = $m;
        }

        $connections = $scene->getConnections();

        foreach ($connections as $connection) {
        	$c = array(
        		"label" => $connection->getLabel(),
        		"pattern" => $connection->getPattern(),
        		"conditions" => $connection->getConditions(),
        		"childSceneId" => $connection->getChildScene()->getId(),
        	);

        	$result["connections"][] = $c;
        }


        return new JSONResponse($result);
    }
}
