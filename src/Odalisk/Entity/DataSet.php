<?php

namespace Odalisk\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Odalisk\DataSet
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class DataSet
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
     * @var string $creationDate
     *
     * @ORM\Column(name="creation_date", type="date")
     */
    private $creationDate;

    /**
     * @var string $description
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var date $lastUpdate
     *
     * @ORM\Column(name="last_update", type="datetime")
     */
    private $lastUpdate;

    /**
     * @var string $tags
     *
     * @ORM\Column(name="tags", type="string", length=64)
     */
    private $tags;

    /**
     * @var string $permissions
     *
     * @ORM\Column(name="permissions", type="string", length=32)
     */
    private $permissions;

    /**
     * @var string $dataProvider
     *
     * @ORM\Column(name="data_provider", type="string", length=64)
     */
    private $dataProvider;

    /**
     * @var string $dataOwner
     *
     * @ORM\Column(name="data_owner", type="string", length=64)
     */
    private $dataOwner;

    /**
     * @var string $timePeriod
     *
     * @ORM\Column(name="time_period", type="string", length=32)
     */
    private $timePeriod;

    /**
     * @var string $rating
     *
     * @ORM\Column(name="rating", type="integer")
     */
    private $rating;


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
     * Set creationDate
     *
     * @param date $creationDate
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;
    }

    /**
     * Get creationDate
     *
     * @return date 
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * Set description
     *
     * @param text $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get description
     *
     * @return text 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set lastUpdate
     *
     * @param string $lastUpdate
     */
    public function setLastUpdate($lastUpdate)
    {
        $this->lastUpdate = $lastUpdate;
    }

    /**
     * Get lastUpdate
     *
     * @return string 
     */
    public function getLastUpdate()
    {
        return $this->lastUpdate;
    }

    /**
     * Set tags
     *
     * @param string $tags
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    /**
     * Get tags
     *
     * @return string 
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set permissions
     *
     * @param string $permissions
     */
    public function setPermissions($permissions)
    {
        $this->permissions = $permissions;
    }

    /**
     * Get permissions
     *
     * @return string 
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * Set dataProvider
     *
     * @param string $dataProvider
     */
    public function setDataProvider($dataProvider)
    {
        $this->dataProvider = $dataProvider;
    }

    /**
     * Get dataProvider
     *
     * @return string 
     */
    public function getDataProvider()
    {
        return $this->dataProvider;
    }

    /**
     * Set dataOwner
     *
     * @param string $dataOwner
     */
    public function setDataOwner($dataOwner)
    {
        $this->dataOwner = $dataOwner;
    }

    /**
     * Get dataOwner
     *
     * @return string 
     */
    public function getDataOwner()
    {
        return $this->dataOwner;
    }

    /**
     * Set timePeriod
     *
     * @param string $timePeriod
     */
    public function setTimePeriod($timePeriod)
    {
        $this->timePeriod = $timePeriod;
    }

    /**
     * Get timePeriod
     *
     * @return string 
     */
    public function getTimePeriod()
    {
        return $this->timePeriod;
    }

    /**
     * Set rating
     *
     * @param integer $rating
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
    }

    /**
     * Get rating
     *
     * @return integer 
     */
    public function getRating()
    {
        return $this->rating;
    }
}
