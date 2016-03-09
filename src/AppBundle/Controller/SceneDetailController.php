<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SceneDetailController extends Controller
{
    /**
     * @Route("/scene/detail/{id}", name="sceneDetail")
     */
    public function indexAction(Request $request)
    {

        return $this->render('writer/sceneDetail.html.twig');
    }
}
