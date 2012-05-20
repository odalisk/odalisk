<?php

namespace Odalisk\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Odalisk\Entity\Category
 *
 * @ORM\Table(name="categories")
 * @ORM\Entity(repositoryClass="Odalisk\Repository\CategoryRepository")
 */
class Category
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
     * @var string $category
     *
     * @ORM\Column(name="category", type="string", length=255)
     */
    private $category;

    /**
     * @var array $aliases
     *
     * @ORM\Column(name="aliases", type="array")
     */
    private $aliases;
    
    public function __construct($category, $aliases = array()) {
        $this->setCategory($category);
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
     * Set category
     *
     * @param string $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * Get category
     *
     * @return string 
     */
    public function getCategory()
    {
        return $this->category;
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