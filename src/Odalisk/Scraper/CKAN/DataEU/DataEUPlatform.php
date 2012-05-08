<?php

namespace Odalisk\Scraper\CKAN\DataEU;

use Odalisk\Scraper\CKAN\BaseCKAN;

class DataEUPlatform extends BaseCKAN {
    public function __construct() {
        $this->criteria = array(
            'setName' => '//h1[@class="page_heading"]'
			//, 'posted_information' = '//div[@id="tagline"]'
			, 'setSummary' => '//div[@class="notes"]/p'
			, 'setReleasedOn' => '//td[.="date_released" and @class="dataset-label"]/../td[2]'
			, 'setLastUpdatedOn' => '//td[.="date_updated" and @class="dataset-label"]/../td[2]'
			, 'setProvider' => '//td[.="published_by" and @class="dataset-label"]/../td[2]'
			, 'setLicense' => '/li[@id="dataset-license" and @class="sidebar-section"]'
        );

		$this->date_format = 'Y-m-d';
    }
    
    public function getDatasetsUrls() {
        // Make the API call
        $response = $this->buzz->get(
            $this->api_url,
            $this->buzz_options
        );
        // Get the paths
        if(200 == $response->getStatusCode()) {
            $data = json_decode($response->getContent());
            
            foreach($data as $key => $dataset_name) {
                $data[$key] = $this->base_url . $dataset_name;
            }
        } else {
            error_log('Couldn\'t fetch list of datasets for ' . $this->name);
        }     
        
        $this->total_count = count($data);
        
        return $data;
    }

	public function parsePortal() {
        $this->portal = new \Odalisk\Entity\Portal();
        $this->portal->setName($this->getName());
        $this->portal->setUrl('http://publicdata.eu/');
        
        $this->em->persist($this->portal);
        $this->em->flush();
	}
}
