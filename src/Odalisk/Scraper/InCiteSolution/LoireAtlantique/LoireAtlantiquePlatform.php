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
		$datasets_urls = array();
		$uids = array();

		$i = 0;
		while(true) {
			echo($i);
			$response = $this->buzz->get($this->datasets_list_url.  $i);
			$crawler  = new Crawler($response->getContent());

			$nodes = $crawler->filterXPath('//td[@class="first"]/h3/a');
			if(count($nodes) > 0) {
				$hrefs = $nodes->extract(array('href'));
				foreach($hrefs as $href) {
					if(preg_match("/\[uid\]=([0-9]+)$/", $href, $match)) {
						$uids[] = $match[1];
					} else {
						error_log('Marche pôs : '.$href.' !');
					}
				}
			} else {
				break;
			}

			$i++;
		}

		foreach($uids as $uid) {
            $formRequest = $factory->createFormRequest();
            $formRequest->setMethod(Message\Request::METHOD_POST);
            $formRequest->fromUrl($this->sanitize($this->base_url . $uid));
            $formRequest->addHeaders($this->buzz_options);
            $formRequest->setFields(array('tx_icsoddatastore_pi1[cgu]' => 'on'));
            self::$urls[] = $formRequest;
        }
        
        $this->total_count = count(self::$urls);
        
        return(self::$urls);
    }

    public function parsePortal() {
        $this->portal = new \Odalisk\Entity\Portal();
        $this->portal->setName($this->getName());
        $this->portal->setUrl('http://data.loire-atlantique.fr/');
        
        $this->em->persist($this->portal);
        $this->em->flush();
    }
}


