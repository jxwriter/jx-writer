<?php
// src/AppBundle/Controller/LuckyController.php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class LuckyController extends Controller
{
    /**
     * @Route("/lucky/number", name="lucky")
     */
    public function numberAction()
    {
        $number = rand(0, 100);

        return $this->render('lucky/lucky.html.twig', array("number" => $number));

    }
}
?>