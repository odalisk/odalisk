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
    protected $id;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var string $class_name
     *
     * @ORM\Column(name="class_name", type="string", length=255)
     */
    protected $class_name;

    /**
     * @var string $url
     *
     * @ORM\Column(name="url", type="string", length=255)
     */
    protected $url;

    /**
     * @var string $base_url
     *
     * @ORM\Column(name="base_url", type="string", length=255)
     */
    protected $base_url;

    /**
     * @var datetime $crawled_at
     *
     * @ORM\Column(name="crawled_at", type="datetime", nullable=TRUE)
     */
    protected $crawled_at = NULL;

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

    /**
     * Set base_url
     *
     * @param string $baseUrl
     */
    public function setBaseUrl($baseUrl)
    {
        $this->base_url = $baseUrl;
    }

    /**
     * Get base_url
     *
     * @return string 
     */
    public function getBaseUrl()
    {
        return $this->base_url;
    }

    /**
     * Set class_name
     *
     * @param string $className
     */
    public function setClassName($className)
    {
        $this->class_name = $className;
    }

    /**
     * Get class_name
     *
     * @return string 
     */
    public function getClassName()
    {
        return $this->class_name;
    }
}