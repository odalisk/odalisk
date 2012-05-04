<?php

namespace Odalisk\Scraper\UK;

use Odalisk\Scraper\BasePlatform;

/**
 * The scraper for data.gov.uk
 */
class UkPlatform extends BasePlatform {
    public function __construct() {
        $this->criteria = array(
            'name' => 'h1.title',
        );
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
}
