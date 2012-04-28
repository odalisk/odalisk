<?php

namespace Odalisk\Scraper\InCiteSolution;

use Odalisk\Scraper\BasePlatform;

/**
 * The scraper for in cite Solution Plateform
 */
abstract class BaseInCiteSolution extends BasePlatform {

    public function __construct() {
        $this->criteria = array(
            'Category' => '.tx_icsoddatastore_pi1_categories > span.value',
            'Licence' => '.tx_icsoddatastore_pi1_licence > span.value',
            'Update Frequency' => '.tx_icsoddatastore_pi1_updatefrequency > span.value',
            'Date of publication' => '.tx_icsoddatastore_pi1_releasedate > span.value',
            'Last update' => '.tx_icsoddatastore_pi1_updatedate > span.value',
            'Description' => '.tx_icsoddatastore_pi1_description > span.value',
            'Manager' => '.tx_icsoddatastore_pi1_manager > span.value',
            'Owner' => '.tx_icsoddatastore_pi1_owner > span.value',
            'Technical data' => '.tx_icsoddatastore_pi1_technical_data > span.value',
        );
    }
    
    public function getDatasetsUrls() {
        // Make the API call
        $this->buzz->getClient()->setTimeout(10);
        $response = $this->buzz->get(
            $this->api_url,
            $this->buzz_options
        );
        // Get the paths
        if(200 == $response->getStatusCode()) {
            $data = json_decode($response->getContent());
            $urls = array();
            
            foreach($data->opendata->answer->data->dataset as $dataset) {
                $urls[] = $this->sanitize($this->base_url . '?tx_icsoddatastore_pi1[uid]=' . $dataset->id);
            }
        } else {
            error_log('Couldn\'t fetch list of datasets for ' . $this->name);
        }     
        
        $this->total_count = count($urls);
        
        return $urls;
    }
    
    public function sanitize($url) {
        return str_replace(']', '%5D', str_replace('[', '%5B', $url));
    }
}
