<?php

namespace Odalisk\Scraper\InCiteSolution\LoireAtlantique;

use Odalisk\Scraper\InCiteSolution\BaseInCiteSolution;

/**
 * The scraper for data.loire-atlantique.fr
 */
class LoireAtlantiquePlatform extends BaseInCiteSolution {

    public function __construct() {
        parent::__construct();
    }

    public function getDatasetsUrls() {

        for ($i=18; $i<162; $i++) {
            $urls[] = $this->sanitize($this->base_url . '?tx_icsoddatastore_pi1[uid]=' . $i);
        }
        
        $this->total_count = count($urls);
        
        return $urls;
    }

}


