<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\Writer\Project;
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

    protected function makeScene($title, $project){
        $scene = new Scene();
        $scene->setTitle($title);
        $scene->setProject($project);

        $em = $this->getDoctrine()->getManager();
        $em->persist($scene);

        return $scene;
    }

    /**
     * @Route("/init", name="fixture")
     */
    public function indexAction(Request $request)
    {
        
        $em = $this->getDoctrine()->getManager();

        $projectRepo = $this->getDoctrine()->getRepository('AppBundle:Writer\Project');
        $project = $projectRepo->find(1);

        $this->addFlash("warning", "Fixtures : Aborted (non-empty db)");
        return $this->redirectToRoute('homepage');
 
        $project = new Project();
        $project->setTitle("Projet démo");

        $em->persist($project);

        $welcome = $this->makeScene("Welcome", $project);
        $bureau = $this->makeScene("Bureau", $project);
        $salon = $this->makeScene("Salon", $project);
        $secret = $this->makeScene("Secret", $project);
        $fin = $this->makeScene("Fin", $project);
        $finHappy = $this->makeScene("Fin !", $project);

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
        
        $em->flush();

        $this->addFlash("notice", "Fixtures : OK.");
        return $this->redirectToRoute('homepage');
    }
}
