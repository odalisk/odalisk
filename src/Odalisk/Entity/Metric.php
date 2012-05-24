<?php

namespace Odalisk\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Odalisk\Entity\Metric
 *
 * @ORM\Table(name="metric")
 * @ORM\Entity(repositoryClass="Odalisk\Repository\MetricRepository")
 */
class Metric
{
	/**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
	private $id;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
	private $name;


    /**
     * @var string $description
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    //private $description;

    /**
     * @var string $coefficient
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    //private $coefficient;

    /**
     * @var string $score
     *
     * @ORM\Column(name="score", type="float")
     */
	private $score;

    /**
     * @ORM\OneToMany(targetEntity="Metric", mappedBy="parent")
     */
	private $subsections;

    /**
     * @ORM\ManyToOne(targetEntity="Metric", inversedBy="subsections")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
	private $parent;

    public function __construct()
    {
        $this->subsections = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set score
     *
     * @param integer $score
     */
    public function setScore($score)
    {
        $this->score = $score;
    }

    /**
     * Get score
     *
     * @return integer 
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * Add subsections
     *
     * @param Odalisk\Entity\Metric $subsections
     */
    public function addMetric(\Odalisk\Entity\Metric $subsections)
    {
        $this->subsections[] = $subsections;
    }

    /**
     * Get subsections
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getSubsections()
    {
        return $this->subsections;
    }

    /**
     * Set parent
     *
     * @param Odalisk\Entity\Metric $parent
     */
    public function setParent(\Odalisk\Entity\Metric $parent)
    {
        $this->parent = $parent;
    }

    /**
     * Get parent
     *
     * @return Odalisk\Entity\Metric 
     */
    public function getParent()
    {
        return $this->parent;
    }
}