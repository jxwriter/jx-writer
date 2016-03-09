<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\Writer\Media;
use AppBundle\Entity\Writer\Scene;
use AppBundle\Entity\Writer\SceneConnection;
use AppBundle\Entity\Writer\Product;

class FixtureController extends Controller
{
    
    protected function makeMediaText($content, $inScene=null) {
        $media = new Media();
        $media->setFormat("text");
        $media->setContent($content);

        if ($inScene) {
            $media->setScene($inScene);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($media);

        return $media;
    }

    protected function makeConnection($scene1, $scene2, $label="", $pattern=""){
        $connection = new SceneConnection();
        $connection->setParentScene($scene1);
        $connection->setChildScene($scene2);

        $connection->setLabel($label);
        $connection->setPattern($pattern);

        $em = $this->getDoctrine()->getManager();
        $em->persist($connection);

        return $connection;
    }

    protected function makeScene($title){
        $scene = new Scene();
        $scene->setTitle($title);

        $em = $this->getDoctrine()->getManager();
        $em->persist($scene);

        return $scene;
    }

    /**
     * @Route("/init", name="fixture")
     */
    public function indexAction(Request $request)
    {

        $welcome = $this->makeScene("Welcome");
        $bureau = $this->makeScene("Bureau");
        $salon = $this->makeScene("Salon");
        $secret = $this->makeScene("Secret");
        $fin = $this->makeScene("Fin");
        $finHappy = $this->makeScene("Fin !");

        $this->makeMediaText("Bienvenue dans la maison démo !", $welcome);
        $this->makeMediaText("Vous arrivez dans le bureau", $bureau);
        $this->makeMediaText("Vous arrivez dans le salon, il est vide.", $salon);
        $this->makeMediaText("En fouillant, vous trouvez ce que vous cherchez.", $secret);
        $this->makeMediaText("Vous quitter la maison démo.", $fin);
        $this->makeMediaText("Vous quitter la maison démo, heureux.", $finHappy);

        $this->makeConnection($welcome, $bureau, "Aller dans le bureau");
        $this->makeConnection($welcome, $salon, "Aller dans le salon");

        $this->makeConnection($bureau, $secret, "", "FOUILLER");
        $this->makeConnection($bureau, $salon, "Vers le salon");
        $this->makeConnection($bureau, $fin, "Quitter la maison démo");

        $this->makeConnection($salon, $bureau, "Vers le bureau");
        $this->makeConnection($salon, $fin, "Quitter la maison démo");

        $this->makeConnection($secret, $finHappy, "Quitter la maison démo");
        
        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $this->redirectToRoute('homepage');
    }
}
