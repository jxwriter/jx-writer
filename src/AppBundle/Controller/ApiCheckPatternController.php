<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\JsonResponse;

class ApiCheckPatternController extends Controller
{
   
    protected $variableChecker;
    protected $variables;

    /**
     * @Route("/api/connection/{sceneId}", name="apiCheckConnection")
     */
    public function indexAction(Request $request, $sceneId)
    {
        $this->variableChecker = $this->get('variable_checker');
        $this->variables = $this->extractRequestVariables($request);

        $sceneRepo = $this->getDoctrine()->getRepository('AppBundle:Writer\Scene');
        $scene = $sceneRepo->find($sceneId);
        
        if (! $request->query->get("_input")){
            return $this->makeJsonResponse(["No input"]);
        }

        $isDebug = $request->query->get("_debug");

        $playerInput = $this->cleanInput($request->query->get("_input"));

        $result = [];
        $result["debug"] = [];

        $connections = $scene->getConnections();

        foreach ($connections as $connection) {

            if (! $connection->getPattern()) {
                continue;
            }

            $resConnection = $this->variableChecker->check($this->variables, $connection->getConditions(), $result["debug"]);
            $resTarget = $this->variableChecker->check($this->variables, $connection->getChildScene()->getConditions(), $result["debug"]);

            if (! $resConnection || ! $resTarget) {
                continue;
            }

            $patterns = explode(";", $connection->getPattern());
            
            foreach ($patterns as $pattern) {
                $match = $this->checkPattern($playerInput, $pattern, $result["debug"]);
                
                if ($match) {
                    $result["debug"][] = "Match !";
                    $result["debug"][] = "Matched scene : " .  $connection->getChildScene()->getTitle() . " #" . $connection->getChildScene()->getId();
                    if ($isDebug) return $this->makeJsonResponse($result);
                    else return $this->redirectToRoute('apiGetScene', ["sceneId"=>$connection->getChildScene()->getId()]);    
                } else {
                    $result["debug"][] = "no match.";
                }
            }

            //


            
        }
        
        $result["result"][] = "No match for this input.";

        if (!$isDebug){
            unset($result["debug"]);
        }

        return $this->makeJsonResponse($result);
    }

    protected function cleanInput($input){
        return strtolower(trim($input));
    }

    protected function checkPattern($input, $pattern, &$log){
        $pattern = $this->cleanInput($pattern);

        $log[] = "Checking [$input] against [$pattern]";

        if (strpos($pattern, "*")===false) {
            $log[] = "...simple comparaison";
            return ($pattern==$input);
        } 


        $regexp = str_replace(["\\*", "\\?"], [".*", "."], preg_quote($pattern));
        $regexp = "/^$regexp\$/";

        $match = [];
        $res = preg_match($regexp, $input, $match);
        
        $log[] = "...preg_match comparaison : [$regexp]";

        return $res;
    }

    protected function makeJsonResponse($content) {
        $response = new JSONResponse($content);
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



   
}
