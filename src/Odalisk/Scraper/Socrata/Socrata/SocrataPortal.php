<?php

namespace Odalisk\Scraper\Socrata\Socrata;

use Odalisk\Scraper\Socrata\BaseSocrataPortal;

class SocrataPortal extends BaseSocrataPortal {

    public function __construct() {
        parent::__construct();
        $this->datasetsListUrl = 'https://opendata.socrata.com/browse?&page=';
    }
}
