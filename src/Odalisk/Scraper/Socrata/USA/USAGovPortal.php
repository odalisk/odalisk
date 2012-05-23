<?php

namespace Odalisk\Scraper\Socrata\USA;

use Odalisk\Scraper\Socrata\BaseSocrataPortal;

class USAGovPortal extends BaseSocrataPortal
{
    public function __construct()
    {
        parent::__construct();
        $this->datasetsListUrl = 'https://explore.data.gov/catalog/raw?&page=';
        $this->batch_size = 25;
    }
}
