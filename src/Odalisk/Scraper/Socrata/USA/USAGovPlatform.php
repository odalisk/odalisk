<?php

namespace Odalisk\Scraper\Socrata\USA;

use Odalisk\Scraper\Socrata\BaseSocrata;

class USAGovPlatform extends BaseSocrata {

    public function __construct() {
		parent::__construct();
    }

	public function parsePortal() {
        $this->portal = new \Odalisk\Entity\Portal();
        $this->portal->setName($this->getName());
        $this->portal->setUrl('http://www.data.gov/');
        
        $this->em->persist($this->portal);
        $this->em->flush();
	}
}
