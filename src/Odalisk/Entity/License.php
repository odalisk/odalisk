<?php

namespace Odalisk\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Odalisk\Entity\License
 *
 * @ORM\Table(name="licenses")
 * @ORM\Entity(repositoryClass="Odalisk\Repository\LicenseRepository")
 */
class License
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
     * @var array $name
     *
     * @ORM\Column(name="name", type="")
     */
    private $alias;

    /**
     * @var string $authorship
     *
     * @ORM\Column(name="authorship", type="string", length=255)
     */
    private $authorship;

    /**
     * @var string $reuse
     *
     * @ORM\Column(name="reuse", type="string", length=255)
     */
    private $reuse;

    /**
     * @var string $redistribution
     *
     * @ORM\Column(name="redistribution", type="string", length=255)
     */
    private $redistribution;

    /**
     * @var string $commercial
     *
     * @ORM\Column(name="commercial", type="string", length=255)
     */
    private $commercial;


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
     * Set authorship
     *
     * @param string $authorship
     */
    public function setAuthorship($authorship)
    {
        $this->authorship = $authorship;
    }

    /**
     * Get authorship
     *
     * @return string 
     */
    public function getAuthorship()
    {
        return $this->authorship;
    }

    /**
     * Set reuse
     *
     * @param string $reuse
     */
    public function setReuse($reuse)
    {
        $this->reuse = $reuse;
    }

    /**
     * Get reuse
     *
     * @return string 
     */
    public function getReuse()
    {
        return $this->reuse;
    }

    /**
     * Set redistribution
     *
     * @param string $redistribution
     */
    public function setRedistribution($redistribution)
    {
        $this->redistribution = $redistribution;
    }

    /**
     * Get redistribution
     *
     * @return string 
     */
    public function getRedistribution()
    {
        return $this->redistribution;
    }

    /**
     * Set commercial
     *
     * @param string $commercial
     */
    public function setCommercial($commercial)
    {
        $this->commercial = $commercial;
    }

    /**
     * Get commercial
     *
     * @return string 
     */
    public function getCommercial()
    {
        return $this->commercial;
    }
}