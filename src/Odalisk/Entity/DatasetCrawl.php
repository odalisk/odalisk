<?php

namespace Odalisk\Entity;
 
use Doctrine\ORM\Mapping as ORM;
 
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="datasets_crawl")
 * @ORM\Entity(repositoryClass="Odalisk\Repository\DatasetCrawlRepository")
 */
class DatasetCrawl {
    
    public function __construct($url, $hash, $code, $portal) {
        $this->setUrl($url);
        $this->setHash($hash);
        $this->setCode($code);
        $this->setPortal($portal);
    }
    
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="Portal")
     */
    protected $portal;
    
    /**
     * @var string $url
     *
     * @ORM\Column(name="url", type="string", length=255)
     */
    protected $url;
    
    /**
     * @var string $hash
     * 
     * @ORM\Column(name="hash", type="string", length="32")
     */
    protected $hash;
    
    /**
     * @var int $code
     * 
     * @ORM\Column(name="code", type="integer", nullable=true)
     */
    protected $code;
    
    /**
     * @var string $crawled_at When did we create this record
     *
     * @ORM\Column(name="crawled_at", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    protected $crawled_at;

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
     * Set hash
     *
     * @param string $hash
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
    }

    /**
     * Get hash
     *
     * @return string 
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Set code
     *
     * @param integer $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * Get code
     *
     * @return integer 
     */
    public function getCode()
    {
        return $this->code;
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
     * Set portal
     *
     * @param Odalisk\Entity\Portal $portal
     */
    public function setPortal(\Odalisk\Entity\Portal $portal)
    {
        $this->portal = $portal;
    }

    /**
     * Get portal
     *
     * @return Odalisk\Entity\Portal 
     */
    public function getPortal()
    {
        return $this->portal;
    }
}