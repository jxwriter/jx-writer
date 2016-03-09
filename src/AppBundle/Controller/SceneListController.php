<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SceneListController extends Controller
{
    /**
     * @Route("/scene/list", name="sceneList")
     */
    public function indexAction(Request $request)
    {


    /*
$product = $this->getDoctrine()
        ->getRepository('AppBundle:Product')
        ->find($id);
    */

        /*$query = $repository->createQueryBuilder('p')
    ->where('p.price > :price')
    ->setParameter('price', '19.99')
    ->orderBy('p.price', 'ASC')
    ->getQuery();

$products = $query->getResult();*/

        return $this->render('writer/sceneList.html.twig');
    }
}
