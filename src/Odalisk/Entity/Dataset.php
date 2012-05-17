<?php
 
namespace Odalisk\Entity;
 
use Doctrine\ORM\Mapping as ORM;
 
use Gedmo\Mapping\Annotation as Gedmo;
 
/**
 * @ORM\Table(name="datasets")
 * @ORM\Entity
 */
class Dataset
{   
     
    public function __construct(array $values = array()) {
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
        foreach($values as $name => $value) {
            call_user_func(array($this, $name), $value);
        }
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
     * @var string $category
     *
     * @ORM\Column(name="category", type="string", nullable=true, length=255)
     */
    protected $category;
     
    /**
     * @var string $released_on When did we create this record
     *
     * @ORM\Column(name="released_on", type="datetime", nullable=true)
     */
    protected $released_on;
     
    /**
     * @var string $last_updated_on When did we create this record
     *
     * @ORM\Column(name="last_updated_on", type="datetime", nullable=true)
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
     * @var string $license
     * 
     * @ORM\Column(name="license", type="string", nullable=true, length=255)
     */
    protected $license;
 
    /**
     * @ORM\ManyToOne(targetEntity="Portal", inversedBy="data_sets")
     * @ORM\JoinColumn(name="portal_id", referencedColumnName="id")
     */
    protected $portal;
 
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
     * Set released_on
     *
     * @param datetime $releasedOn
     */
    public function setReleasedOn($releasedOn)
    {
        $this->released_on = $releasedOn;
    }
 
    /**
     * Get released_on
     *
     * @return datetime 
     */
    public function getReleasedOn()
    {
        return $this->released_on;
    }
 
    /**
     * Set last_updated_on
     *
     * @param datetime $lastUpdatedOn
     */
    public function setLastUpdatedOn($lastUpdatedOn)
    {
        $this->last_updated_on = $lastUpdatedOn;
    }
 
    /**
     * Get last_updated_on
     *
     * @return datetime 
     */
    public function getLastUpdatedOn()
    {
        return $this->last_updated_on;
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
     * Set license
     *
     * @param string $license
     */
    public function setLicense($license)
    {
        $this->license = $license;
    }
 
    /**
     * Get license
     *
     * @return string 
     */
    public function getLicense()
    {
        return $this->license;
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