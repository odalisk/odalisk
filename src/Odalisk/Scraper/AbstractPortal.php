<?php

namespace Odalisk\Scraper;

abstract class AbstractPortal {
    
    protected $buzz;
    
    protected $base_url;
    
    public function __construct($buzz, $base_url) {
        $this->buzz = $buzz;
        $this->base_url = $base_url;
    }
    
    abstract public function getDatasets();
}