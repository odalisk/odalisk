<?php

namespace Odalisk\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Odalisk\Entity\License
 *
 * @ORM\Table(name="licenses")
 * @ORM\Entity
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
    protected $id;

    /**
     * @var array $name
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @ORM\Column(name="aliases", type="array")
     */
    protected $aliases;

    /**
     * @var string $authorship
     *
     * @ORM\Column(name="authorship", type="boolean")
     */
    protected $authorship;

    /**
     * @var string $reuse
     *
     * @ORM\Column(name="reuse", type="boolean")
     */
    protected $reuse;

    /**
     * @var string $redistribution
     *
     * @ORM\Column(name="redistribution", type="boolean")
     */
    protected $redistribution;

    /**
     * @var string $commercial
     *
     * @ORM\Column(name="commercial", type="boolean")
     */
    protected $commercial;

    public function __construct($name)
    {
        $this->setName($name);
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
     * Set aliases
     *
     * @param array $aliases
     */
    public function setAliases($aliases)
    {
        $this->aliases = $aliases;
    }

    /**
     * Add aliases
     *
     * @param array $alias
     */
    public function addAlias($alias)
    {
        $this->aliases[] = $alias;
    }

    /**
     * Get aliases
     *
     * @return array
     */
    public function getAliases()
    {
        return $this->aliases;
    }

    /**
     * Return true if the license is a good one, false otherwise.
     */
    public function isGoodLicense() {
        return(
                $this->getAuthorship() &&
                $this->getReuse() &&
                $this->getRedistribution() &&
                $this->getCommercial()
              );
    }

    /**
     * Return the quality of the license in pourcentage
     */
     public function getQuality() {
         $quality = 0;
         $quality+= (int)$this->getAuthorship() * 25;
         $quality+= (int)$this->getReuse() * 25;
         $quality+= (int)$this->getRedistribution() * 25;
         $quality+= (int)$this->getCommercial() * 25;

         return($quality);
     }
}