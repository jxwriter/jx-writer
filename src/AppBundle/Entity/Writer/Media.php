<?php

namespace AppBundle\Entity\Writer;

use Doctrine\ORM\Mapping as ORM;

/**
 * Media
 *
 * @ORM\Table(name="writer_media")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Writer\MediaRepository")
 */
class Media
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @ORM\Column(name="format", type="string", length=255)
     */
    private $format;

    /**
     * @ORM\Column(name="conditions", type="text", nullable=TRUE)
     */
    private $conditions;

    /**
     * @ORM\ManyToOne(targetEntity="Scene", inversedBy="medias")
     * @ORM\JoinColumn(name="scene_id", referencedColumnName="id")
     */
    private $scene;

    /**
     * @ORM\Column(type="integer")
     */
    private $position = 0;

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
     * Set content
     *
     * @param string $content
     *
     * @return Media
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set format
     *
     * @param string $format
     *
     * @return Media
     */
    public function setFormat($format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * Get format
     *
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Set conditions
     *
     * @param string $conditions
     *
     * @return Media
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
     * Set scene
     *
     * @param \AppBundle\Entity\Writer\Scene $scene
     *
     * @return Media
     */
    public function setScene(\AppBundle\Entity\Writer\Scene $scene = null)
    {
        $this->scene = $scene;

        return $this;
    }

    /**
     * Get scene
     *
     * @return \AppBundle\Entity\Writer\Scene
     */
    public function getScene()
    {
        return $this->scene;
    }
}
