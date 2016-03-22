<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\JsonResponse;

class ApiGetSceneController extends Controller
{
   
    protected $variableChecker;
    protected $variables;

    /**
     * @Route("/api/scene/{sceneId}", name="apiGetScene")
     */
    public function indexAction(Request $request, $sceneId)
    {
        $this->variableChecker = $this->get('variable_checker');
        $this->variables = $this->extractRequestVariables($request);

        $sceneRepo = $this->getDoctrine()->getRepository('AppBundle:Writer\Scene');
        
        $scene = $sceneRepo->find($sceneId);
        
        
        $result = array();
        
        try {
            $receivedVariableString = implode(",", array_keys($this->variables));
            $result['debug'][] = "Notice: received variables: " . $receivedVariableString;
        
            $this->processScene($scene, $result);
            $this->processMedias($scene, $result);
            $this->processConnections($scene, $result);
            $this->processActions($scene, $result);

        } catch (Exception $e) {
            $result['debug'][] = $e;
        }
        
        $result['debug'][] = "Done.";

        if ($request->query->get("_debug") != 1) {
            unset($result['debug']);
        }

        $response = new JSONResponse($result);
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }

    protected function extractRequestVariables($request) {

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

    protected function extractActions($actionString) {

        $actions = explode(";", $actionString);
        $extract = array();

        foreach ($actions as $action) {
            $pattern = "/(\w+)([\+\-])(\w+)/";
            $match = array();
            $res = preg_match($pattern, $action, $match);

            if (!$res || count($match) != 4) {
                continue;
            }

            $variable = $match[1];
            $sign = $match[2];
            $value = $match[3];

            $extract[$variable] = $value * ($sign."1");
        }

        return $extract;
    }

    protected function processScene($scene, &$result){
        $result["debug"][] = "Checking Scene";
        $res = $this->variableChecker->check($this->variables, $scene->getConditions(), $result["debug"]);

        if (! $res) {
            throw new Exception("Forbidden scene");
        }

        $result["id"] = $scene->getId();
        $result["title"] = $scene->getTitle();
        $result["project"] = array(
            "id" => $scene->getProject()->getId(),
            "title" => $scene->getProject()->getTitle(),
        );
        $result["conditions"] = $scene->getConditions();
        $result["medias"] = array();
        $result["connections"] = array();
        $result["actions"] = $scene->getActions();
        
    }

    protected function processMedias($scene, &$result){
        $medias = $scene->getMedias();

        foreach ($medias as $media) {
            $result["debug"][] = "Checking Media";
            
            $res = $this->variableChecker->check(
                $this->variables, 
                $media->getConditions(), 
                $result["debug"]
            );

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
    }

    protected function processConnections($scene, &$result){
        $connections = $scene->getConnections();

        foreach ($connections as $connection) {
            $result["debug"][] = "Checking Connection + target scene";
            $resConnection = $this->variableChecker->check($this->variables, $connection->getConditions(), $result["debug"]);
            $resTarget = $this->variableChecker->check($this->variables, $connection->getChildScene()->getConditions(), $result["debug"]);

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
    }

    protected function processActions($scene, &$result){

        $actions = $this->extractActions($scene->getActions());
        $result["actions"] = $actions;
    }

   
}
