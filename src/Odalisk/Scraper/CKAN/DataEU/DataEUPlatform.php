<?php

namespace Odalisk\Scraper\CKAN\DataEU;

use Odalisk\Scraper\CKAN\BaseCKAN;

class DataEUPlatform extends BaseCKAN {
    public function __construct() {
        $this->criteria = array(
            'setName' => '//h1[@class="page_heading"]',
            'setSummary' => '//div[@class="notes"]/p',
            'setReleasedOn' => '//td[.="date_released" and @class="dataset-label"]/../td[2]',
            'setLastUpdatedOn' => '//td[.="date_updated" and @class="dataset-label"]/../td[2]',
            'setProvider' => '//td[.="published_by" and @class="dataset-label"]/../td[2]',
            'setLicense' => '/li[@id="dataset-license" and @class="sidebar-section"]',
            'setCategories' => '//td[text()=\'categories\']/following-sibling::*',
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

    public function parsePortal() {
        $this->portal = new \Odalisk\Entity\Portal();
        $this->portal->setName($this->getName());
        $this->portal->setUrl('http://publicdata.eu/');

        $this->em->persist($this->portal);
        $this->em->flush();
    }
}
