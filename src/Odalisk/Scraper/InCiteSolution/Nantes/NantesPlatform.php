<?php

namespace Odalisk\Scraper\InCiteSolution\Nantes;

use Odalisk\Scraper\InCiteSolution\BaseInCiteSolution;

/**
 * The scraper for data.nantes.fr
 */
class NantesPlatform extends BaseInCiteSolution {
    public function __construct() {
        parent::__construct();
    }
    
    public function parsePortal() {
        $this->portal = new \Odalisk\Entity\Portal();
        $this->portal->setName($this->getName());
        $this->portal->setUrl('http://data.nantes.fr/');
        
        $this->em->persist($this->portal);
        $this->em->flush();
    }
}


