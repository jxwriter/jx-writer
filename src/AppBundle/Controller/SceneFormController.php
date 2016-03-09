<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SceneFormController extends Controller
{
    /**
     * @Route("/scene/form/{id?}", name="sceneForm")
     */
    public function indexAction(Request $request)
    {

        return $this->render('writer/sceneForm.html.twig');
    }
}
