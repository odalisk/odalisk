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
     * @var bool $is_open
     *
     * @ORM\Column(name="is_open", type="boolean")
     */
	private $is_open;

    /**
     * @var bool $has_spec
     *
     * @ORM\Column(name="has_spec", type="boolean")
     */
	private $has_spec;

    /**
     * @var bool $is_computer_readable
     *
     * @ORM\Column(name="is_computer_readable", type="boolean")
     */
	private $is_computer_readable;

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

    /**
     * Set is_open
     *
     * @param boolean $isOpen
     */
    public function setIsOpen($isOpen)
    {
        $this->is_open = $isOpen;
    }

    /**
     * Get is_open
     *
     * @return boolean 
     */
    public function getIsOpen()
    {
        return $this->is_open;
    }

    /**
     * Set has_spec
     *
     * @param boolean $hasSpec
     */
    public function setHasSpec($hasSpec)
    {
        $this->has_spec = $hasSpec;
    }

    /**
     * Get has_spec
     *
     * @return boolean 
     */
    public function getHasSpec()
    {
        return $this->has_spec;
    }

    /**
     * Set is_computer_readable
     *
     * @param boolean $isComputerReadable
     */
    public function setIsComputerReadable($isComputerReadable)
    {
        $this->is_computer_readable = $isComputerReadable;
    }

    /**
     * Get is_computer_readable
     *
     * @return boolean 
     */
    public function getIsComputerReadable()
    {
        return $this->is_computer_readable;
    }
}