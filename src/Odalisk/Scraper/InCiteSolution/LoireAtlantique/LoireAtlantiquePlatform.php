<?php

namespace Odalisk\Scraper\InCiteSolution\LoireAtlantique;

use Odalisk\Scraper\InCiteSolution\BaseInCiteSolution;

use Symfony\Component\DomCrawler\Crawler;
use Buzz\Message;

/**
 * The scraper for data.loire-atlantique.fr
 */
class LoireAtlantiquePlatform extends BaseInCiteSolution {

	private $datasets_list_url;

    public function __construct() {
        parent::__construct();
		$this->datasets_list_url = 'http://data.loire-atlantique.fr/donnees/?tx_icsoddatastore_pi1[page]=';
    }

    public function getDatasetsUrls() {
        $factory = new Message\Factory();
		$urls = array();

		$i = 0;
		while(true) {
			$response = $this->buzz->get($this->datasets_list_url . $i);
			$crawler  = new Crawler($response->getContent());

			$nodes = $crawler->filterXPath('//td[@class="first"]/h3/a');
			if(count($nodes) > 0) {
				$hrefs = $nodes->extract(array('href'));
				foreach($hrefs as $href) {
					if(preg_match("/\[uid\]=([0-9]+)$/", $href, $match)) {
						$urls[] = $this->base_url . $match[1];
					} else {
						error_log('Marche pôs : '.$href.' !');
					}
				}
			} else {
				break;
			}

			$i++;
		}
        
        $this->total_count = count($urls);
        
        return($urls);
    }

    public function parsePortal() {
        $this->portal = new \Odalisk\Entity\Portal();
        $this->portal->setName($this->getName());
        $this->portal->setUrl('http://data.loire-atlantique.fr/');
        
        $this->em->persist($this->portal);
        $this->em->flush();
    }
}


