<?php

namespace Odalisk\Entity;

use Doctrine\ORM\Mapping as ORM;

use Gedmo\Mapping\Annotation as Gedmo;


/**
 * Odalisk\Statistics
 *
 * @ORM\Table(name="statistics")
 * @ORM\Entity(repositoryClass="Odalisk\Repository\StatisticsRepository")
 */
class Statistics
{

    /**
     * @var integer $datasets_count
     *
     * @ORM\Column(name="datasets_count", type="integer")
    */
    protected $datasets_count;

    /**
     * @var integer $inChargePersonCount
     *
     * @ORM\Column(name="inChargePersonCount", type="integer")
     */
    protected $inChargePersonCount;


    /**
     * @var integer $releasedOnExistCount
     *
     * @ORM\Column(name="released_on_count", type="integer")
     */
    protected $releasedOnExistCount;

    /**
     * @var integer $lastUpdateOnExistCount
     *
     * @ORM\Column(name="last_update_on_count", type="integer")
     */
    protected $lastUpdatedOnExistCount;

    /**
     * @var integer $categoryExistCount
     *
     * @ORM\Column(name="category_count", type="integer")
     */
    protected $categoryExistCount;

    /**
     * @var integer $summaryAndTitleCount
     *
     * @ORM\Column(name="summary_title_count", type="integer")
     */
    protected $summaryAndTitleCount;

    /**
     * @var integer $licenseCount
     *
     * @ORM\Column(name="license_count", type="integer")
     */
    protected $licenseCount;


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
     * @ORM\OneToOne(targetEntity="Portal")
     * @ORM\Id
     * @ORM\JoinColumn(name="portal_id", referencedColumnName="id")
     */
    protected $portal;


    /**
     * Set datasets_count
     *
     * @param integer $datasetsCount
     */
    public function setDatasetsCount($datasetsCount)
    {
        $this->datasets_count = $datasetsCount;
    }

    /**
     * Get datasets_count
     *
     * @return integer 
     */
    public function getDatasetsCount()
    {
        return $this->datasets_count;
    }

    /**
     * Set inChargePersonCount
     *
     * @param integer $inChargePersonCount
     */
    public function setInChargePersonCount($inChargePersonCount)
    {
        $this->inChargePersonCount = $inChargePersonCount;
    }

    /**
     * Get inChargePersonCount
     *
     * @return integer 
     */
    public function getInChargePersonCount()
    {
        return $this->inChargePersonCount;
    }

    /**
     * Set releasedOnExistCount
     *
     * @param integer $releasedOnExistCount
     */
    public function setReleasedOnExistCount($releasedOnExistCount)
    {
        $this->releasedOnExistCount = $releasedOnExistCount;
    }

    /**
     * Get releasedOnExistCount
     *
     * @return integer 
     */
    public function getReleasedOnExistCount()
    {
        return $this->releasedOnExistCount;
    }

    /**
     * Set lastUpdatedOnExistCount
     *
     * @param integer $lastUpdatedOnExistCount
     */
    public function setLastUpdatedOnExistCount($lastUpdatedOnExistCount)
    {
        $this->lastUpdatedOnExistCount = $lastUpdatedOnExistCount;
    }

    /**
     * Get lastUpdatedOnExistCount
     *
     * @return integer 
     */
    public function getLastUpdatedOnExistCount()
    {
        return $this->lastUpdatedOnExistCount;
    }

    /**
     * Set categoryExistCount
     *
     * @param integer $categoryExistCount
     */
    public function setCategoryExistCount($categoryExistCount)
    {
        $this->categoryExistCount = $categoryExistCount;
    }

    /**
     * Get categoryExistCount
     *
     * @return integer 
     */
    public function getCategoryExistCount()
    {
        return $this->categoryExistCount;
    }

    /**
     * Set summaryAndTitleCount
     *
     * @param integer $summaryAndTitleCount
     */
    public function setSummaryAndTitleCount($summaryAndTitleCount)
    {
        $this->summaryAndTitleCount = $summaryAndTitleCount;
    }

    /**
     * Get summaryAndTitleCount
     *
     * @return integer 
     */
    public function getSummaryAndTitleCount()
    {
        return $this->summaryAndTitleCount;
    }

    /**
     * Set licenseCount
     *
     * @param integer $licenseCount
     */
    public function setLicenseCount($licenseCount)
    {
        $this->licenseCount = $licenseCount;
    }

    /**
     * Get licenseCount
     *
     * @return integer 
     */
    public function getLicenseCount()
    {
        return $this->licenseCount;
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