<?php

namespace Odalisk\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Odalisk\Entity\DatasetCriteria
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
     * @var string $is_title_and_summary
     *
     * @ORM\Column(name="is_title_and_summary", type="boolean", nullable=false)
     */
    protected $is_title_and_summary;

    /**
     * @var string $is_released_on
     *
     * @ORM\Column(name="is_released_on", type="boolean", nullable=false)
     */
    protected $is_released_on;

    /**
     * @var string $is_last_update_on
     *
     * @ORM\Column(name="is_last_update_on", type="boolean", nullable=false)
     */
    protected $is_last_update_on;

    /**
     * @var string $is_provider
     *
     * @ORM\Column(name="is_provider", type="boolean", nullable=false)
     */
    protected $is_provider;

    /**
     * @var string $is_owner
     *
     * @ORM\Column(name="is_owner", type="boolean", nullable=false)
     */
    protected $is_owner;

    /**
     * @var string $is_maintainer
     *
     * @ORM\Column(name="is_maintainer", type="boolean", nullable=false)
     */
    protected $is_maintainer;

    /**
     * @var string $is_good_license
     *
     * @ORM\Column(name="is_good_license", type="boolean", nullable=false)
     */
    protected $is_good_license = false;

    /**
     * @var string $is_good_license
     *
     * @ORM\Column(name="license_quality", type="integer")
     */
    protected $license_quality = 0;

    /**
     * @var string $is_at_least_one_good_format
     *
     * @ORM\Column(name="is_at_least_one_good_format", type="boolean", nullable=false)
     */
    protected $is_at_least_one_good_format = false;
    
    public function __construct($d) {
        $this->setIsTitleAndSummary($this->not_empty($d->getName() . $d->getSummary()));
        $this->setIsOwner($this->not_empty($d->getOwner()));
        $this->setIsProvider($this->not_empty($d->getProvider() . $d->getOwner()));
        $this->setIsMaintainer($this->not_empty($d->getMaintainer()));
        $this->setIsReleasedOn($this->not_empty($d->getReleasedOn()));
        $this->setIsLastUpdateOn($this->not_empty($d->getLastUpdatedOn()));
        
        if(($license = $d->getLicense()) !== null) {
            $this->setIsGoodLicense($license->getIsGood());
            $this->setLicenseQuality($license->getQuality());
        }
        
        if(($formats = $d->getFormats()) !== null) {
            foreach($formats as $format) {
                if($format->getIsGood()) {
                    $this->setIsAtLeastOneGoodFormat(true);
                    break;
                }
            }
        }
    }
    
    private function not_empty($s) {
        return !empty($s);
    }

    /**
     * Set is_title_and_summary
     *
     * @param boolean $isTitleAndSummary
     */
    public function setIsTitleAndSummary($isTitleAndSummary)
    {
        $this->is_title_and_summary = $isTitleAndSummary;
    }

    /**
     * Get is_title_and_summary
     *
     * @return boolean
     */
    public function getIsTitleAndSummary()
    {
        return $this->is_title_and_summary;
    }

    /**
     * Set is_released_on
     *
     * @param boolean $isReleasedOn
     */
    public function setIsReleasedOn($isReleasedOn)
    {
        $this->is_released_on = $isReleasedOn;
    }

    /**
     * Get is_released_on
     *
     * @return boolean
     */
    public function getIsReleasedOn()
    {
        return $this->is_released_on;
    }

    /**
     * Set is_last_update_on
     *
     * @param boolean $isLastUpdateOn
     */
    public function setIsLastUpdateOn($isLastUpdateOn)
    {
        $this->is_last_update_on = $isLastUpdateOn;
    }

    /**
     * Get is_last_update_on
     *
     * @return boolean
     */
    public function getIsLastUpdateOn()
    {
        return $this->is_last_update_on;
    }

    /**
     * Set is_provider
     *
     * @param boolean $isProvider
     */
    public function setIsProvider($isProvider)
    {
        $this->is_provider = $isProvider;
    }

    /**
     * Get is_provider
     *
     * @return boolean
     */
    public function getIsProvider()
    {
        return $this->is_provider;
    }

    /**
     * Set is_owner
     *
     * @param boolean $isOwner
     */
    public function setIsOwner($isOwner)
    {
        $this->is_owner = $isOwner;
    }

    /**
     * Get is_owner
     *
     * @return boolean
     */
    public function getIsOwner()
    {
        return $this->is_owner;
    }

    /**
     * Set is_maintainer
     *
     * @param boolean $isMaintainer
     */
    public function setIsMaintainer($isMaintainer)
    {
        $this->is_maintainer = $isMaintainer;
    }

    /**
     * Get is_maintainer
     *
     * @return boolean
     */
    public function getIsMaintainer()
    {
        return $this->is_maintainer;
    }

    /**
     * Set is_good_license
     *
     * @param boolean $isGoodLicense
     */
    public function setIsGoodLicense($isGoodLicense)
    {
        $this->is_good_license = $isGoodLicense;
    }

    /**
     * Get is_good_license
     *
     * @return boolean
     */
    public function getIsGoodLicense()
    {
        return $this->is_good_license;
    }

    /**
     * Set is_at_least_one_good_format
     *
     * @param boolean $isAtLeastOneGoodFormat
     */
    public function setIsAtLeastOneGoodFormat($isAtLeastOneGoodFormat)
    {
        $this->is_at_least_one_good_format = $isAtLeastOneGoodFormat;
    }

    /**
     * Get is_at_least_one_good_format
     *
     * @return boolean
     */
    public function getIsAtLeastOneGoodFormat()
    {
        return $this->is_at_least_one_good_format;
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
}