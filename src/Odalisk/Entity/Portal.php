<?php

namespace Odalisk\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Odalisk\Portal
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Portal
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
     * @var string $url
     *
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;

    /**
     * @var datetime $crawled_at
     *
     * @ORM\Column(name="crawled_at", type="datetime")
     */
    private $crawled_at;


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
     * Set url
     *
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set crawled_at
     *
     * @param datetime $crawledAt
     */
    public function setCrawledAt($crawledAt)
    {
        $this->crawled_at = $crawledAt;
    }

    /**
     * Get crawled_at
     *
     * @return datetime 
     */
    public function getCrawledAt()
    {
        return $this->crawled_at;
    }
}