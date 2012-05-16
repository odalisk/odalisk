<?php

namespace Odalisk\Scraper\CKAN\UK;

use Odalisk\Scraper\CKAN\BaseCKAN;

/**
 * The scraper for data.gov.uk
 */
class UKPlatform extends BaseCKAN {
    public function __construct() {
        $this->criteria = array(
            'setName' => '//td[.="Name" and @class="package_label"]/../td[2]/div[1]'
			//, 'posted_information' = '//div[@id="tagline"]'
			, 'setSummary' => '//div[@class="package_title"]'
			, 'setReleasedOn' => '//td[.="Released" and @class="package_label"]/../td[2]/div[1]'
			, 'setLastUpdatedOn' => '//td[.="Last updated" and @class="package_label"]/../td[2]/div[1]'
			, 'setProvider' => '//td[.="Published by" and @class="package_label"]/../td[2]/div[1]'
			, 'setLicense' => '//td[.="Licence" and @class="package_label"]/../td[2]/div[1]'
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
        $this->portal->setUrl('http://data.gov.uk/');
        
        $this->em->persist($this->portal);
        $this->em->flush();
	}
}
