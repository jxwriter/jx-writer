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

    public function makeEmptyScene($persist=false){
        $scene = new Scene();

        if ($persist){
            $this->entityManager->persist($scene);
        }

        return $scene;
    }

    public function makeEmptyMedia($persist=false){
        $media = new Media();

        if ($persist){
            $this->entityManager->persist($media);
        }

        return $media;
    }

    public function makeEmptyConnection($persist=false){
        $connection = new SceneConnection();

        if ($persist){
            $this->entityManager->persist($connection);
        }

        return $connection;
    }

    public function makeProject($title){
        $project = new Project();
        $project->setTitle($title);

        $this->entityManager->persist($project);

        return $project;
    }

    public function loadOrEmptyScene($id){
        if (!$id) {
            return $this->makeEmptyScene(true);
        }

        return $this->entityManager->getRepository('AppBundle:Writer\Scene')->findOneById($id);
    }

    public function loadOrEmptyMedia($id){
        if (!$id) {
            return $this->makeEmptyMedia(true);
        }

        return $this->entityManager->getRepository('AppBundle:Writer\Media')->findOneById($id);
    }

    public function loadOrEmptyConnection($id){
        if (!$id) {
            return $this->makeEmptyConnection(true);
        }

        return $this->entityManager->getRepository('AppBundle:Writer\SceneConnection')->findOneById($id);
    }

}
