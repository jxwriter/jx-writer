<?php

namespace AppBundle\Entity\Writer;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Scene
 *
 * @ORM\Table(name="writer_scene")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Writer\SceneRepository")
 */
class Scene
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(name="conditions", type="text", nullable=TRUE)
     */
    private $conditions;

    /**
     * @ORM\Column(name="actions", type="text", nullable=TRUE)
     */
    private $actions;

    /**
     * @ORM\ManyToOne(targetEntity="Project")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     */
    private $project;


    /**
     * @ORM\OneToMany(targetEntity="Media", mappedBy="scene")
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $medias;

    /**
     * @ORM\OneToMany(targetEntity="SceneConnection", mappedBy="parentScene")
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $connections;

    /**
     * @ORM\Column(name="description", type="text", nullable=TRUE)
     */
    private $description = "";

    /**
     * @ORM\Column(name="data", type="text", nullable=TRUE)
     */
    private $data="";


    public function __construct()
    {
        $this->medias = new ArrayCollection();
        $this->connections = new ArrayCollection();
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Scene
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set conditions
     *
     * @param string $conditions
     *
     * @return Scene
     */
    public function setConditions($conditions)
    {
        $this->conditions = $conditions;

        return $this;
    }

    /**
     * Get conditions
     *
     * @return string
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * Set actions
     *
     * @param string $actions
     *
     * @return Scene
     */
    public function setActions($actions)
    {
        $this->actions = $actions;

        return $this;
    }

    /**
     * Get actions
     *
     * @return string
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * Set project
     *
     * @param \AppBundle\Entity\Writer\Project $project
     *
     * @return Scene
     */
    public function setProject(\AppBundle\Entity\Writer\Project $project = null)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return \AppBundle\Entity\Writer\Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Add media
     *
     * @param \AppBundle\Entity\Writer\Media $media
     *
     * @return Scene
     */
    public function addMedia(\AppBundle\Entity\Writer\Media $media)
    {
        $this->medias[] = $media;

        return $this;
    }

    /**
     * Remove media
     *
     * @param \AppBundle\Entity\Writer\Media $media
     */
    public function removeMedia(\AppBundle\Entity\Writer\Media $media)
    {
        $this->medias->removeElement($media);
    }

    /**
     * Get medias
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMedias()
    {
        return $this->medias;
    }

    /**
     * Add Connection
     *
     * @param \AppBundle\Entity\Writer\SceneConnection $connection
     *
     * @return Scene
     */
    public function addConnection(\AppBundle\Entity\Writer\SceneConnection $connection)
    {
        $this->connections[] = $connection;

        return $this;
    }

    /**
     * Remove Connection
     *
     * @param \AppBundle\Entity\Writer\SceneConnection $connection
     */
    public function removeConnection(\AppBundle\Entity\Writer\SceneConnection $connection)
    {
        $this->connections->removeElement($connection);
    }

    /**
     * Get Connections
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getConnections()
    {
        return $this->connections;
    }

    /**
     * Set data
     *
     * @param string $data
     *
     * @return Scene
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Scene
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}
