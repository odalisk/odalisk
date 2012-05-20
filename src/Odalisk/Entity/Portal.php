<?php

namespace Odalisk\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Gedmo\Mapping\Annotation as Gedmo;


/**
 * Odalisk\Portal
 *
 * @ORM\Table(name="portals")
 * @ORM\Entity(repositoryClass="Odalisk\Repository\PortalRepository")
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
     * @var string $url
     *
     * @ORM\Column(name="url", type="string", length=255)
     */
    protected $url;


    /**
     * @var string $country
     *
     * @ORM\Column(name="country", type="string", length=255)
     */
    protected $country;

    /**
     * @var string $country
     *
     * @ORM\Column(name="entity", type="string", length=255)
     */
    protected $entity;

    /**
     * @var string $status
     *
     * @ORM\Column(name="status", type="string", length=255)
     */
    protected $status;

    /**
     * @var string $created_at When did we create this record
     *
     * @ORM\Column(name="created_at", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    protected $created_at;

    /**
     * @var string $updated_at When did we update this record
     *
     * @ORM\Column(name="updated_at", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    protected $updated_at;

    /**
     * @ORM\OneToMany(targetEntity="Dataset", mappedBy="portal")
     */
    protected $datasets;

    public function __construct()
    {
        $this->datasets = new ArrayCollection();
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
     * Set country
     *
     * @param string $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set entity
     *
     * @param string $entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }

    /**
     * Get entity
     *
     * @return string
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Set status
     *
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
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
     * Add datasets
     *
     * @param Odalisk\Entity\Dataset $dataset
     */
    public function addDataset(\Odalisk\Entity\Dataset $dataset)
    {
        if (!$this->datasets->contains($dataset)) {
            $this->datasets[] = $dataset;
            $dataset->setPortal($this);
        }
    }

    /**
     * Get datasets
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getDatasets()
    {
        return $this->datasets;
    }

    /**
     * Set created_at
     *
     * @param datetime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;
    }

    /**
     * Get created_at
     *
     * @return datetime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set updated_at
     *
     * @param datetime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updated_at = $updatedAt;
    }

    /**
     * Get updated_at
     *
     * @return datetime
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }
}