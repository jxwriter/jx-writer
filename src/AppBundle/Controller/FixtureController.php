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
    /**
     * @Route("/init", name="fixture")
     */
    public function indexAction(Request $request)
    {
        $entityFactory = $this->get('entity_factory');
        $em = $this->getDoctrine()->getManager();
        
        $projectRepo = $this->getDoctrine()->getRepository('AppBundle:Writer\Project');
        $project = $projectRepo->find(1);

        if ($project!=null) {
            $this->addFlash("warning", "Fixtures : Aborted (non-empty db)");
            return $this->redirectToRoute('homepage');
        }
 
        $project = new Project();
        $project->setTitle("Projet démo");

        $em->persist($project);

        $welcome = $entityFactory->makeScene("Welcome", $project);
        $jardin = $entityFactory->makeScene("Jardin", $project);
        $jardin->setActions("inspiration+1");

        $bureau = $entityFactory->makeScene("Bureau", $project);
        $salon = $entityFactory->makeScene("Salon", $project);
        $secret = $entityFactory->makeScene("Secret", $project);
        $fin = $entityFactory->makeScene("Fin", $project);
        $finHappy = $entityFactory->makeScene("Fin !", $project);

        $entityFactory->makeMediaText("Bienvenue dans la maison démo !", $welcome);
        $entityFactory->makeMediaText("Un grand bol d'air, ca fait du bien !", $jardin);
        $entityFactory->makeMediaText("Vous arrivez dans le bureau", $bureau);
        $entityFactory->makeMediaText("On dirait qu'il cache quelque chose", $bureau)->setConditions("inspiration>0");
        $entityFactory->makeMediaText("Vous arrivez dans le salon, il est vide.", $salon);
        $entityFactory->makeMediaText("En fouillant, vous trouvez ce que vous cherchez.", $secret);
        $entityFactory->makeMediaText("Vous quittez la maison démo.", $fin);
        $entityFactory->makeMediaText("Vous quittez la maison démo, heureux.", $finHappy);

        $entityFactory->makeConnection($welcome, $bureau, "Aller dans le bureau");
        $entityFactory->makeConnection($welcome, $salon, "Aller dans le salon");
        $entityFactory->makeConnection($welcome, $jardin, "Aller dans le jardin");

        $entityFactory->makeConnection($jardin, $welcome, "Retourner dans l'entrée");    

        $entityFactory->makeConnection($bureau, $secret, "", "FOUILLE*;CHERCHE*;TIROIR");
        $entityFactory->makeConnection($bureau, $secret, "Fouiller le bureau")->setConditions("inspiration>=2");
        $entityFactory->makeConnection($bureau, $salon, "Vers le salon");
        $entityFactory->makeConnection($bureau, $fin, "Quitter la maison démo");

        $entityFactory->makeConnection($salon, $bureau, "Vers le bureau");
        $entityFactory->makeConnection($salon, $fin, "Quitter la maison démo");
        $entityFactory->makeConnection($salon, $welcome, "Retourner dans l'entrée");   

        $entityFactory->makeConnection($secret, $finHappy, "Quitter la maison démo");
        
        $em = $this->getDoctrine()->getManager();
        $em->flush();

        $this->addFlash("notice", "Fixtures : OK.");
        return $this->redirectToRoute('homepage');
    }
}
