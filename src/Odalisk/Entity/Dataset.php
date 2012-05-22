<?php

namespace Odalisk\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="datasets")
 * @ORM\Entity
 */
class Dataset
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
     * @var string $url
     *
     * @ORM\Column(name="url", type="string", nullable=true, length=255)
     */
    protected $url;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", nullable=true, length=255)
     */
    protected $name;

    /**
     * @var string $summary
     *
     * @ORM\Column(name="summary", type="text", nullable=true)
     */
    protected $summary;


    /**
     * @ORM\Column(name="raw_categories", type="string", nullable=true, length=255)
     */
    protected $raw_categories;
    
    /**
     * @ORM\ManyToMany(targetEntity="Odalisk\Entity\Category")
     */
    protected $categories;

    /**
     * @ORM\Column(name="raw_formats", type="string", nullable=true, length=255)
     */
    protected $raw_formats;
    
    /**
     * @var string $format
     *
     * @ORM\ManyToMany(targetEntity="Odalisk\Entity\Format")
     */
    protected $formats;

    /**
     * @ORM\OneToOne(targetEntity="Odalisk\Entity\DatasetCriteria")
     */
    protected $criteria;
    
    /**
     * @var string $license
     *
     * @ORM\Column(name="raw_license", type="string", nullable=true, length=255)
     */
    protected $raw_license;

    /**
     * @ORM\ManyToMany(targetEntity="Odalisk\Entity\License")
     */
    protected $license;

    /**
     * @var string $released_on When did we create this record
     *
     * @ORM\Column(name="released_on", type="string", nullable=true, length=255)
     */
    protected $released_on;

    /**
     * @var string $last_updated_on When did we create this record
     *
     * @ORM\Column(name="last_updated_on", type="string", nullable=true, length=255)
     */
    protected $last_updated_on;

    /**
     * @var string $provider
     *
     * @ORM\Column(name="provider", type="string", nullable=true, length=255)
     */
    protected $provider;

    /**
     * @var string $owner
     *
     * @ORM\Column(name="owner", type="string", nullable=true, length=255)
     */
    protected $owner;

    /**
     * @var string $maintainer
     *
     * @ORM\Column(name="maintainer", type="string", nullable=true, length=255)
     */
    protected $maintainer;


    /**
     * @ORM\ManyToOne(targetEntity="Portal", inversedBy="data_sets")
     * @ORM\JoinColumn(name="portal_id", referencedColumnName="id")
     */
    protected $portal;
    
    public function __construct(array $values = array()) {
        $this->categories = new ArrayCollection();
        $this->formats = new ArrayCollection();
        $this->license = new ArrayCollection();
        $this->populate($values);
    }

    /**
     * Builds the entity from the array
     *
     * array(
     *  'setUrl' => 'http://some.url',
     *  'setName' => 'A name'
     * )
     *
     * @param array $values
     */
    public function populate(array $values = array()) {
        foreach ($values as $name => $value) {
            call_user_func(array($this, $name), $value);
        }
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
     * Set summary
     *
     * @param text $summary
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;
    }

    /**
     * Get summary
     *
     * @return text 
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * Set raw_categories
     *
     * @param string $rawCategories
     */
    public function setRawCategories($rawCategories)
    {
        $this->raw_categories = $rawCategories;
    }

    /**
     * Get raw_categories
     *
     * @return string 
     */
    public function getRawCategories()
    {
        return $this->raw_categories;
    }

    /**
     * Set raw_formats
     *
     * @param string $rawFormats
     */
    public function setRawFormats($rawFormats)
    {
        $this->raw_formats = $rawFormats;
    }

    /**
     * Get raw_formats
     *
     * @return string 
     */
    public function getRawFormats()
    {
        return $this->raw_formats;
    }

    /**
     * Set raw_license
     *
     * @param string $rawLicense
     */
    public function setRawLicense($rawLicense)
    {
        $this->raw_license = $rawLicense;
    }

    /**
     * Get raw_license
     *
     * @return string 
     */
    public function getRawLicense()
    {
        return $this->raw_license;
    }

    /**
     * Set released_on
     *
     * @param string $releasedOn
     */
    public function setReleasedOn($releasedOn)
    {
        $this->released_on = $releasedOn;
    }

    /**
     * Get released_on
     *
     * @return string 
     */
    public function getReleasedOn()
    {
        return $this->released_on;
    }

    /**
     * Set last_updated_on
     *
     * @param string $lastUpdatedOn
     */
    public function setLastUpdatedOn($lastUpdatedOn)
    {
        $this->last_updated_on = $lastUpdatedOn;
    }

    /**
     * Get last_updated_on
     *
     * @return string 
     */
    public function getLastUpdatedOn()
    {
        return $this->last_updated_on;
    }

    /**
     * Set provider
     *
     * @param string $provider
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;
    }

    /**
     * Get provider
     *
     * @return string 
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * Set owner
     *
     * @param string $owner
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
    }

    /**
     * Get owner
     *
     * @return string 
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set maintainer
     *
     * @param string $maintainer
     */
    public function setMaintainer($maintainer)
    {
        $this->maintainer = $maintainer;
    }

    /**
     * Get maintainer
     *
     * @return string 
     */
    public function getMaintainer()
    {
        return $this->maintainer;
    }

    /**
     * Add categories
     *
     * @param Odalisk\Entity\Category $categories
     */
    public function addCategory(\Odalisk\Entity\Category $categories)
    {
        $this->categories[] = $categories;
    }
    
    public function setCategories(array $categories) {
        foreach($categories as $category) {
            $this->addCategory($category);
        }
    }

    /**
     * Get categories
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Add formats
     *
     * @param Odalisk\Entity\Format $formats
     */
    public function addFormat(\Odalisk\Entity\Format $formats)
    {
        $this->formats[] = $formats;
    }
    
    /**
     * Get formats
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function setFormats(array $formats)
    {
        foreach($formats as $format) {
            $this->addFormat($format);
        }
    }

    /**
     * Get formats
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getFormats()
    {
        return $this->formats;
    }

    /**
     * Set criteria
     *
     * @param Odalisk\Entity\DatasetCriteria $criteria
     */
    public function setCriteria(\Odalisk\Entity\DatasetCriteria $criteria)
    {
        $this->criteria = $criteria;
    }

    /**
     * Get criteria
     *
     * @return Odalisk\Entity\DatasetCriteria 
     */
    public function getCriteria()
    {
        return $this->criteria;
    }

    /**
     * Set license
     *
     * @param Odalisk\Entity\License $license
     */
    public function setLicense(\Odalisk\Entity\License $license)
    {
        $this->license = $license;
    }

    /**
     * Get license
     *
     * @return Odalisk\Entity\License 
     */
    public function getLicense()
    {
        return $this->license;
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