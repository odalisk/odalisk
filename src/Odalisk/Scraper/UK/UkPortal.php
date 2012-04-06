<?php

namespace Odalisk\Scraper\UK;

use Symfony\Component\DomCrawler\Crawler;

use Odalisk\Scraper\AbstractPortal;

/**
 * The scraper for data.nantes.fr
 */
class UkPortal extends AbstractPortal {
    
    public function __construct($buzz) {
        parent::__construct($buzz, 'http://data.gov.uk/dataset/');
    }
    
    public function getDatasets() {
        return new UkDatasetCollection($this->buzz, $this->base_url);
    }
}