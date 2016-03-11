<?php

namespace AppBundle\Entity\Writer;

use Doctrine\ORM\Mapping as ORM;

/**
 * SceneConnection
 *
 * @ORM\Table(name="writer_scene_connection")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Writer\SceneConnectionRepository")
 */
class SceneConnection
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="conditions", type="text", nullable=TRUE)
     */
    private $conditions;

    /**
     * @ORM\Column(name="pattern", type="string", length=255, nullable=TRUE)
     */
    private $pattern;

    /**
     * @ORM\Column(name="label", type="string", length=255, nullable=TRUE)
     */
    private $label;

    /**
     * @ORM\ManyToOne(targetEntity="Scene", inversedBy="connections")
     * @ORM\JoinColumn(name="parent_scene_id", referencedColumnName="id")
     */
    private $parentScene;

    /**
     * @ORM\ManyToOne(targetEntity="Scene")
     * @ORM\JoinColumn(name="child_scene_id", referencedColumnName="id")
     */
    private $childScene;

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
     * Set conditions
     *
     * @param string $conditions
     *
     * @return SceneConnection
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
     * Set pattern
     *
     * @param string $pattern
     *
     * @return SceneConnection
     */
    public function setPattern($pattern)
    {
        $this->pattern = $pattern;

        return $this;
    }

    /**
     * Get pattern
     *
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * Set label
     *
     * @param string $label
     *
     * @return SceneConnection
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set parentScene
     *
     * @param \AppBundle\Entity\Writer\Scene $parentScene
     *
     * @return SceneConnection
     */
    public function setParentScene(\AppBundle\Entity\Writer\Scene $parentScene = null)
    {
        $this->parentScene = $parentScene;

        return $this;
    }

    /**
     * Get parentScene
     *
     * @return \AppBundle\Entity\Writer\Scene
     */
    public function getParentScene()
    {
        return $this->parentScene;
    }

    /**
     * Set childScene
     *
     * @param \AppBundle\Entity\Writer\Scene $childScene
     *
     * @return SceneConnection
     */
    public function setChildScene(\AppBundle\Entity\Writer\Scene $childScene = null)
    {
        $this->childScene = $childScene;

        return $this;
    }

    /**
     * Get childScene
     *
     * @return \AppBundle\Entity\Writer\Scene
     */
    public function getChildScene()
    {
        return $this->childScene;
    }

    /**
     * Set position
     *
     * @param integer $position
     *
     * @return SceneConnection
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }
}
