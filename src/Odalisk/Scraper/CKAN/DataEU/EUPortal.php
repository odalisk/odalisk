<?php

namespace Odalisk\Scraper\CKAN\DataEU;

use Odalisk\Scraper\CKAN\BaseCkanPortal;

class EUPortal extends BaseCkanPortal {
    public function __construct() {
        $this->criteria = array(
            'setName' => '//h1[@class="page_heading"]',
            'setSummary' => './/*[@id="notes-extract"]/p',
            'setReleasedOn' => '//td[.="date_released" and @class="dataset-label"]/../td[2]',
            'setOwner' => './/*[@property="dc:creator"]',
            'setMaintainer' => './/*[@property="dc:contributor"]',
            'setLastUpdatedOn' => '//td[.="date_updated" and @class="dataset-label"]/../td[2]',
            'setProvider' => '//td[.="published_by" and @class="dataset-label"]/../td[2]',
            'setRawLicense' => '/li[@id="dataset-license" and @class="sidebar-section"]',
            'setCategories' => '//td[text()="categories"]/following-sibling::*',
            'setFormats' => './/*[@property="dc:format"]'
        );

        $this->inChargeFields = array('setOwner','setMaintainer');
    }
    
    protected function additionalExtraction($crawler, &$data) 
    {

        // Deal with UTF8
        foreach($data as $key => $value) {
            $data[$key] = utf8_decode($value);
        }

        if (array_key_exists('setCategories', $data)) {
            if(is_array(json_decode($data['setCategories']))){
                $data['setCategories'] = implode(';', json_decode($data['setCategories']));
            }
        }
    }

    protected function additionalNormalization(&$data)
    {
        foreach ($this->inChargeFields as $field) {
            if (array_key_exists($field, $data)) {
                if(preg_match("/not given/i",$data[$field])){
                    unset($data[$field]);
                }
            }
        }
    }
}
