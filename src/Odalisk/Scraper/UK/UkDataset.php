<?php

namespace Odalisk\Scraper\UK;

use Odalisk\Scraper\BaseDataset;

class UkDataset extends BaseDataset {
    public $url;
    
    public function __construct($buzz, $url) {
        parent::__construct($buzz, $url);
        
        $this->buzz_options[] = 'User-agent: Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.0.1) Gecko/2008071615 Fedora/3.0.1-1.fc9 Firefox/3.0.1';
        
        $this->criteria = array(
            'name' => 'h1.title',
        );
    }
}