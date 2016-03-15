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
        $variableChecker = $this->get('variable_checker');
        $variables = $this->extractProjectVariables($request);

        $sceneRepo = $this->getDoctrine()->getRepository('AppBundle:Writer\Scene');
        
        $scene = $sceneRepo->find($sceneId);
        $project = $scene->getProject();
        
        $result = array();
        $result["debug"] = array();

        $result["debug"][] = "Notice: received variables: " . implode(",", array_keys($variables));
        
        $result["debug"][] = "Checking Scene";
        $res = $variableChecker->check($variables, $scene->getConditions(), $result["debug"]);

        
        if (! $res) {
            return new JSONResponse($result);
        }

        $result["id"] = $scene->getId();
        $result["title"] = $scene->getTitle();
        $result["actions"] = $scene->getActions();
        $result["conditions"] = $scene->getConditions();
        $result["medias"] = array();
        $result["connections"] = array();
        
        $medias = $scene->getMedias();

        foreach ($medias as $media) {
            $result["debug"][] = "Checking Media";
            $res = $variableChecker->check($variables, $media->getConditions(), $result["debug"]);

            if (! $res) {
                continue;
            }

        	$m = array(
        		"format" => $media->getFormat(),
        		"content" => $media->getContent(),
        		"conditions" => $media->getConditions(),
        	);

        	$result["medias"][] = $m;
        }

        $connections = $scene->getConnections();

        foreach ($connections as $connection) {
            $result["debug"][] = "Checking Connection + target scene";
            $resConnection = $variableChecker->check($variables, $connection->getConditions(), $result["debug"]);
            $resTarget = $variableChecker->check($variables, $connection->getChildScene()->getConditions(), $result["debug"]);

            if (! $resConnection || ! $resTarget) {
                continue;
            }

        	$c = array(
        		"label" => $connection->getLabel(),
        		"pattern" => $connection->getPattern(),
        		"conditions" => $connection->getConditions(),
        		"childSceneId" => $connection->getChildScene()->getId(),
        	);

        	$result["connections"][] = $c;
        }

        $result["debug"][] = "Done.";
        return new JSONResponse($result);
    }


    protected function extractProjectVariables($request) {

        $variables = array();

        $parameters = $request->query->all();
        
        foreach ($parameters as $key => $value) {
            if ( $key[0] == "_" ) {
                continue;
            }

            $variables[$key] = $value;
        }

        return $variables;
    }

   
}
