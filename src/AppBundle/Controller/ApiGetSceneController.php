<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ApiGetSceneController extends Controller
{
    /**
     * @Route("/api/scene/{id}", name="apiGetScene")
     */
    public function indexAction(Request $request)
    {
        return new JSONResponse(array());
    }
}
