<?php

namespace Odalisk\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Odalhask\Entity\DatasetCriteria
 *
 * @ORM\Table(name="dataset_criteria")
 * @ORM\Entity(repositoryClass="Odalisk\Repository\DatasetCriteriaRepository")
 */
class DatasetCriteria
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
     * @var string $has_title_and_summary
     *
     * @ORM\Column(name="has_title_and_summary", type="boolean", nullable=false)
     */
    protected $has_title_and_summary;

    /**
     * @var string $has_released_on
     *
     * @ORM\Column(name="has_released_on", type="boolean", nullable=false)
     */
    protected $has_released_on;

    /**
     * @var string $has_last_update_on
     *
     * @ORM\Column(name="has_last_update_on", type="boolean", nullable=false)
     */
    protected $has_last_update_on;

    /**
     * @var string $has_provider
     *
     * @ORM\Column(name="has_provider", type="boolean", nullable=false)
     */
    protected $has_provider;

    /**
     * @var string $has_owner
     *
     * @ORM\Column(name="has_owner", type="boolean", nullable=false)
     */
    protected $has_owner;

    /**
     * @var string $has_maintainer
     *
     * @ORM\Column(name="has_maintainer", type="boolean", nullable=false)
     */
    protected $has_maintainer;

    /**
     * @var string $has_good_license
     *
     * @ORM\Column(name="has_good_license", type="boolean", nullable=false)
     */
    protected $has_good_license = false;

    /**
     * @var string $has_good_license
     *
     * @ORM\Column(name="license_quality", type="float")
     */
    protected $license_quality = 0;

    /**
     * @var string $has_category
     *
     * @ORM\Column(name="has_category", type="boolean")
     */
    protected $has_category;

    /**
     * @var string $has_at_least_one_good_format
     *
     * @ORM\Column(name="has_at_least_one_good_format", type="boolean", nullable=false)
     */
    protected $has_at_least_one_good_format = false;
    
    public function __construct($d) {
        $this->setHasTitleAndSummary($this->not_empty($d->getName() . $d->getSummary()));
        $this->setHasOwner($this->not_empty($d->getOwner()));
        $this->setHasProvider($this->not_empty($d->getProvider() . $d->getOwner()));
        $this->setHasMaintainer($this->not_empty($d->getMaintainer()));
        $this->setHasReleasedOn($this->not_empty($d->getReleasedOn()));
        $this->setHasLastUpdateOn($this->not_empty($d->getLastUpdatedOn()));
        $this->setHasCategory($this->not_empty($d->getCategories()));
        
        if(($license = $d->getLicense()) !== null) {
            $this->setHasGoodLicense($license->getIsGood());
            $this->setLicenseQuality($license->getQuality());
        }
        
        if(($formats = $d->getFormats()) !== null) {
            foreach($formats as $format) {
                if($format->getIsGood()) {
                    $this->setHasAtLeastOneGoodFormat(true);
                    break;
                }
            }
        }
    }
    
    private function not_empty($s) {
        return !empty($s);
    }

    /**
     * Set has_title_and_summary
     *
     * @param boolean $hasTitleAndSummary
     */
    public function setHasTitleAndSummary($hasTitleAndSummary)
    {
        $this->has_title_and_summary = $hasTitleAndSummary;
    }

    /**
     * Get has_title_and_summary
     *
     * @return boolean
     */
    public function getHasTitleAndSummary()
    {
        return $this->has_title_and_summary;
    }

    /**
     * Set has_released_on
     *
     * @param boolean $hasReleasedOn
     */
    public function setHasReleasedOn($hasReleasedOn)
    {
        $this->has_released_on = $hasReleasedOn;
    }

    /**
     * Get has_released_on
     *
     * @return boolean
     */
    public function getHasReleasedOn()
    {
        return $this->has_released_on;
    }

    /**
     * Set has_last_update_on
     *
     * @param boolean $hasLastUpdateOn
     */
    public function setHasLastUpdateOn($hasLastUpdateOn)
    {
        $this->has_last_update_on = $hasLastUpdateOn;
    }

    /**
     * Get has_last_update_on
     *
     * @return boolean
     */
    public function getHasLastUpdateOn()
    {
        return $this->has_last_update_on;
    }

    /**
     * Set has_provider
     *
     * @param boolean $hasProvider
     */
    public function setHasProvider($hasProvider)
    {
        $this->has_provider = $hasProvider;
    }

    /**
     * Get has_provider
     *
     * @return boolean
     */
    public function getHasProvider()
    {
        return $this->has_provider;
    }

    /**
     * Set has_owner
     *
     * @param boolean $hasOwner
     */
    public function setHasOwner($hasOwner)
    {
        $this->has_owner = $hasOwner;
    }

    /**
     * Get has_owner
     *
     * @return boolean
     */
    public function getHasOwner()
    {
        return $this->has_owner;
    }

    /**
     * Set has_maintainer
     *
     * @param boolean $hasMaintainer
     */
    public function setHasMaintainer($hasMaintainer)
    {
        $this->has_maintainer = $hasMaintainer;
    }

    /**
     * Get has_maintainer
     *
     * @return boolean
     */
    public function getHasMaintainer()
    {
        return $this->has_maintainer;
    }

    /**
     * Set has_good_license
     *
     * @param boolean $hasGoodLicense
     */
    public function setHasGoodLicense($hasGoodLicense)
    {
        $this->has_good_license = $hasGoodLicense;
    }

    /**
     * Get has_good_license
     *
     * @return boolean
     */
    public function getHasGoodLicense()
    {
        return $this->has_good_license;
    }

    /**
     * Set has_at_least_one_good_format
     *
     * @param boolean $hasAtLeastOneGoodFormat
     */
    public function setHasAtLeastOneGoodFormat($hasAtLeastOneGoodFormat)
    {
        $this->has_at_least_one_good_format = $hasAtLeastOneGoodFormat;
    }

    /**
     * Get has_at_least_one_good_format
     *
     * @return boolean
     */
    public function getHasAtLeastOneGoodFormat()
    {
        return $this->has_at_least_one_good_format;
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
     * Set license_quality
     *
     * @param integer $licenseQuality
     */
    public function setLicenseQuality($licenseQuality)
    {
        $this->license_quality = $licenseQuality;
    }

    /**
     * Get license_quality
     *
     * @return integer 
     */
    public function getLicenseQuality()
    {
        return $this->license_quality;
    }

    /**
     * Set has_category
     *
     * @param boolean $hasCategory
     */
    public function setHasCategory($hasCategory)
    {
        $this->has_category = $hasCategory;
    }

    /**
     * Get has_category
     *
     * @return boolean 
     */
    public function getHasCategory()
    {
        return $this->has_category;
    }
}