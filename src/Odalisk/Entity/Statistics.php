<?php

namespace Odalisk\Entity;

use Doctrine\ORM\Mapping as ORM;

use Gedmo\Mapping\Annotation as Gedmo;


/**
 * Odalisk\Portal
 *
 * @ORM\Table(name="statistics")
 * @ORM\Entity(repositoryClass="Odalisk\Repository\StatisticRepository")
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

    public function getPortal(){
        return $this->portal;
    }

    public function setPortal($portal){
        $this->portal = $portal;
    }

    public function getDatasetCount(){
        return $this->datasets_count;
    }

    public function setDatasetsCount($count){
        $this->datasets_count = $count;
    }

    public function getInChargePersonCount(){
        return $this->inChargePersonCount;
    }

    public function setInChargePersonCount($count){
        $this->inChargePersonCount = $count;
    }

    public function getLastUpdateOnCount(){
        return $this->lastUpdateOnExistCount;
    }

    public function setLastUpdatedOnCount($count){
        $this->lastUpdatedOnExistCount = $count;
    }

    public function getReleasedOnCount(){
        return $this->releasedOnExistCount;
    }

    public function setReleasedOnCount($count){
        $this->releasedOnExistCount = $count;
    }

    public function getCategoryCount(){
        return $this->categoryExistCount;
    }

    public function setCategoryCount($count){
        $this->categoryExistCount = $count;
    }

    public function getSummaryAndTitleCount($count){
        return $this->summaryAndTitleCount;
    }

    public function setSummaryAndTitleCount($count){
        $this->summaryAndTitleCount = $count;
    }
}
