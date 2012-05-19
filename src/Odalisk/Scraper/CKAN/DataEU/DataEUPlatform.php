<?php

namespace Odalisk\Scraper\CKAN\DataEU;

use Odalisk\Scraper\CKAN\BaseCKAN;

class DataEUPlatform extends BaseCKAN {
    public function __construct() {
        $this->criteria = array(
            'setName' => '//h1[@class="page_heading"]',
            'setSummary' => './/*[@id="notes-extract"]/p',
            'setReleasedOn' => '//td[.="date_released" and @class="dataset-label"]/../td[2]',
            'setOwner' => './/*[@property="dc:creator"]',
            'setMaintainer' => './/*[@property="dc:contributor"]',
            'setLastUpdatedOn' => '//td[.="date_updated" and @class="dataset-label"]/../td[2]',
            'setProvider' => '//td[.="published_by" and @class="dataset-label"]/../td[2]',
            'setLicense' => '/li[@id="dataset-license" and @class="sidebar-section"]',
            'setCategory' => '//td[text()="categories"]/following-sibling::*',
            'setFormat' => './/*[@property="dc:format"]'
        );

        $this->dateFormat = 'Y-m-d';
    }
    
    protected function additionalExtraction($crawler, &$data) 
    {
        // Deal with UTF8
        foreach($data as $key => $value) {
            $data[$key] = utf8_decode($value);
        }
    }

    protected function additionalNormalization(&$data)
    {
        $inChargeFields = array('setOwner','setMaintainer');
        foreach ($inChargeFields as $field) {
            if (array_key_exists($field, $data)) {
                if(preg_match("/not given/i",$data[$field])){
                    unset($data[$field]);
                }
            }
        }
    }

    public function parsePortal() {
        $this->portal = new \Odalisk\Entity\Portal();
        $this->portal->setName($this->getName());
        $this->portal->setUrl('http://publicdata.eu/');

        $this->em->persist($this->portal);
        $this->em->flush();
    }
}
