<?php

namespace Odalisk\Scraper;

abstract class BasePortal {
    
    protected $buzz;
    
    protected $buzz_options = array();
    
    protected $base_url;

	protected $datasets_api_url;
    
    public function __construct($buzz, $base_url, $datasets_api_url = '') {
        $this->buzz = $buzz;
        $this->buzz_options[] = 'User-agent: Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.0.1) Gecko/2008071615 Fedora/3.0.1-1.fc9 Firefox/3.0.1';
        
        $this->base_url         = $base_url;
		if( $datasets_api_url ) $this->datasets_api_url = $datasets_api_url;
    }
    
    abstract public function getDatasetsUrls();
}
