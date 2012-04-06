<?php

namespace Odalisk\Scraper\Nantes;

use Symfony\Component\DomCrawler\Crawler;

use Odalisk\Scraper\AbstractPortal;

/**
 * The scraper for data.nantes.fr
 */
class NantesPortal extends AbstractPortal {
    
    public function __construct($buzz) {
        parent::__construct($buzz, 'http://data.nantes.fr/donnees/detail/');
    }
    
    public function getDatasets() {
        return new NantesDatasetCollection($this->buzz, $this->base_url);
    }
}
