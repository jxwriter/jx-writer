<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager as EntityManager;

use AppBundle\Entity\Writer\Project;
use AppBundle\Entity\Writer\Media;
use AppBundle\Entity\Writer\Scene;
use AppBundle\Entity\Writer\SceneConnection;
use AppBundle\Entity\Writer\Product;

class EntityFactory
{
	protected $entityManager;

	public function __construct(EntityManager $entityManager){
		$this->entityManager = $entityManager;
	}

    public function makeMediaText($content, $inScene=null) {
        return $this->makeMedia($content, $inScene, "text");
    }

    public function makeMedia($content, $inScene=null, $format="text") {
        $media = new Media();
        $media->setFormat($format);
        $media->setContent($content);

        if ($inScene) {
            $media->setScene($inScene);
        }

        $this->entityManager->persist($media);

        return $media;
    }


    public function makeConnection($scene1, $scene2, $label="", $pattern=""){
        $connection = new SceneConnection();
        $connection->setParentScene($scene1);
        $connection->setChildScene($scene2);

        $connection->setLabel($label);
        $connection->setPattern($pattern);

        $this->entityManager->persist($connection);

        return $connection;
    }

    public function makeScene($title, $project){
        $scene = new Scene();
        $scene->setTitle($title);
        $scene->setProject($project);

        $this->entityManager->persist($scene);

        return $scene;
    }

    public function makeEmptyScene(){
        $scene = new Scene();
        return $scene;
    }

    public function makeProject($title){
        $project = new Project();
        $project->setTitle($title);

        $this->entityManager->persist($project);

        return $project;
    }

    public function loadOrEmptyScene($id){
        if (!$id) {
            $scene = $this->makeEmptyScene();
            $this->entityManager->persist($scene);
            return $scene;
        }

        return $this->entityManager->getRepository('AppBundle:Writer\Scene')->findOneById($id);

    }

}
