<?php

namespace Odalisk\Scraper\InCiteSolution\LoireAtlantique;

use Odalisk\Scraper\InCiteSolution\BaseInCiteSolution;

use Buzz\Message;

/**
 * The scraper for data.loire-atlantique.fr
 */
class LoireAtlantiquePlatform extends BaseInCiteSolution {

    public function __construct() {
        parent::__construct();
    }

    public function getDatasetsUrls() {

        $factory = new Message\Factory();

        for ($i=18; $i < 163; $i++) {

            $formRequest = $factory->createFormRequest();
            $formRequest->setMethod(Message\Request::METHOD_POST);
            $formRequest->fromUrl($this->sanitize($this->base_url . '?tx_icsoddatastore_pi1[uid]=' . $i));
            $formRequest->addHeaders($this->buzz_options);
            $formRequest->setFields(array('tx_icsoddatastore_pi1[cgu]' => 'on'));
            $urls[] = $formRequest;
        }
        
        $this->total_count = count($urls);
        
        return $urls;
    }

    public function parsePortal() {
        $this->portal = new \Odalisk\Entity\Portal();
        $this->portal->setName($this->getName());
        $this->portal->setUrl('http://data.loire-atlantique.fr/');
        
        $this->em->persist($this->portal);
        $this->em->flush();
    }
}


