<?php

namespace Odalisk\Scraper\Socrata;

use Symfony\Component\DomCrawler\Crawler;

use Odalisk\Scraper\BasePortal;

use Buzz\Message;

class SocrataPortal extends SocrataTypePortal {

    private static $i = 0;
    
	private static $datasets_number = 18776;
    
    public function __construct($buzz) {
        parent::__construct(
				$buzz
				, 'https://opendata.socrata.com/d/'
				, 'https://opendata.socrata.com/api/views.json?limit='.self::$datasets_number
			);
    }
}
