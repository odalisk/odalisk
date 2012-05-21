<?php

namespace Odalisk\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Odalisk\Entity\Format
 *
 * @ORM\Table(name="formats")
 * @ORM\Entity(repositoryClass="Odalisk\Repository\FormatRepository")
 */
class Format
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
     * @var string $format
     *
     * @ORM\Column(name="format", type="string", length=255)
     */
    private $format;

    /**
     * @var array $aliases
     *
     * @ORM\Column(name="aliases", type="array")
     */
    private $aliases;
    
    public function __construct($format, $aliases = array()) {
        $this->setFormat($format);
        $this->setAliases($aliases);
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
     * Set format
     *
     * @param string $format
     */
    public function setFormat($format)
    {
        $this->format = $format;
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
     * Add alias
     *
     * @param string $alias 
     */
    public function addAlias($alias) {
        if(!in_array($alias, $this->aliases)) {
            $this->aliases[] = $alias;
        }
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
     * Get aliases
     *
     * @return array 
     */
    public function getAliases()
    {
        return $this->aliases;
    }
}