<?php

namespace Odalisk\Scraper\Socrata\NY;

use Odalisk\Scraper\Socrata\BaseSocrataPortal;

class NewYorkPortal extends BaseSocrataPortal {
    public function __construct() {
        parent::__construct();
        $this->datasetsListUrl = 'https://nycopendata.socrata.com/browse?&page=';
    }
}
