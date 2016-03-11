<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\Writer\Media;
use AppBundle\Entity\Writer\Scene;
use AppBundle\Entity\Writer\SceneConnection;
use AppBundle\Entity\Writer\Product;

class PreviewProjectController extends Controller
{
    /**
     * @Route("/project/preview/{sceneId}", name="previewProject")
     */
    public function indexAction(Request $request, $sceneId)
    {
        $sceneRepo = $this->getDoctrine()->getRepository('AppBundle:Writer\Scene');
        
        $scene = $sceneRepo->find($sceneId);
        $project = $scene->getProject();

        if ($request->query->has('patternInput')) {
            $pattern = $request->query->get('patternInput');
            $connection = $this->patternConnection($scene, $pattern);

            if ($connection) {
                $this->addFlash("notice", "Found valid pattern : " . $pattern);
                return $this->redirectToRoute('previewProject', array('sceneId'=> $connection->getChildScene()->getId()));
            } else {
                $this->addFlash("warning", "invalid pattern : " . $pattern);
            }

        }

        //$connections = $scene->getConnections();

        $labelConnections = $this->getLabelConnections($scene);
        $patternConnections = $this->getPatternConnections($scene);
       
        return $this->render('writer/previewProject.html.twig', 
            array(
                "project"=>$project, 
                "scene" => $scene,
                "labelConnections" => $labelConnections,
                "patternConnections" => $patternConnections
            ));
    }


    protected function getLabelConnections($scene){
        $connectionRepo = $this->getDoctrine()->getRepository('AppBundle:Writer\SceneConnection');
        
        $builder = $connectionRepo->createQueryBuilder('c');
        $query = $builder
            ->where('c.parentScene = :parentScene')
            ->andWhere($builder->expr()->neq('c.label', ':empty'))
            ->andWhere($builder->expr()->eq('c.pattern', ':empty'))
            ->setParameter('empty', '')
            ->setParameter('parentScene', $scene)
            ->getQuery();

        return $query->getResult();
    }

    protected function getPatternConnections($scene){
        $connectionRepo = $this->getDoctrine()->getRepository('AppBundle:Writer\SceneConnection');
        
        $builder = $connectionRepo->createQueryBuilder('c');
        $query = $builder
            ->where('c.parentScene = :parentScene')
            ->andWhere($builder->expr()->eq('c.label', ':empty'))
            ->andWhere($builder->expr()->neq('c.pattern', ':empty'))
            ->setParameter('empty', '')
            ->setParameter('parentScene', $scene)
            ->getQuery();

        return $query->getResult();
    }

    protected function patternConnection($scene, $pattern){
        $connectionRepo = $this->getDoctrine()->getRepository('AppBundle:Writer\SceneConnection');
        
        $builder = $connectionRepo->createQueryBuilder('c');
        $query = $builder
            ->where('c.parentScene = :parentScene')
            ->andWhere($builder->expr()->eq('c.pattern', ':input'))
            ->setParameter('input', $pattern)
            ->setParameter('parentScene', $scene)
            ->getQuery();

        $connections = $query->getResult();
        
        if (isset($connections[0])) {
            return $connections[0];
        }

        return null;
        
    }
}
