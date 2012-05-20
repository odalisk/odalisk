<?php

namespace Odalisk\Scraper\Socrata\NY;

use Odalisk\Scraper\Socrata\BaseSocrata;

class NYPlatform extends BaseSocrata {
    public function __construct() {
        parent::__construct();
        $this->datasetsListUrl = 'https://nycopendata.socrata.com/browse?&page=';
    }

    public function parsePortal() {
        $this->portal = new \Odalisk\Entity\Portal();
        $this->portal->setName($this->getName());
        $this->portal->setUrl('https://nycopendata.socrata.com/');
        $this->portal->setCountry($this->country);
        $this->portal->setStatus($this->status);
        $this->portal->setEntity($this->entity);
        $this->em->persist($this->portal);
        $this->em->flush();
    }
}
