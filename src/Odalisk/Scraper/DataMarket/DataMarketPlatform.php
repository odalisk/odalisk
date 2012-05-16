<?php

namespace Odalisk\Scraper\DataMarket;

use Symfony\Component\DomCrawler\Crawler;
use Buzz\Message;
use Buzz\Browser;

use Odalisk\Scraper\BasePlatform;

/**
 * The scraper for in DataMarket
 */
class DataMarketPlatform extends BasePlatform {
	// The url on which the datasets are listed.
	private $datasetsListUrl = 'http://datamarket.com/data/list/?q=datatype:dataset';
	// the number of datasets displayed for a request.
	private $batchSize = 20;
	private $datasetsNumber = 0;

    public function __construct() {
        $this->criteria = array(
                          );
             
        $this->date_format = 'd/m/Y';
    }

	/*
	 * For this platform, get all the datesets' urls is a bit more tricky ; it's
	 * imposible to get retrieve all the urls via API. There is a page which
	 * list all the datasets of the website. BUT, it displays only 20 sets at
	 * loading and it's an Ajax request which downloads and displays the 20
	 * followings when scroll down. So, we need to :
	 * - Get the total number of datasets ; information given at :
	 * http://datamarket.com/data/list/?q=datatype%3Adataset
	 * - We know that the sets are displayed by batch of 20 ; we have to request
	 * the site (total_number / 20) times
	 * - For each request, we get fetch the datasets' urls.
	 *
	 * This solution is manually verified.
	 */
    public function getDatasetsUrls() {
		$browser = new Browser();
		$datasets = array();
		
		// We begin by fetching the number of datasets
		$response = $browser->get('http://datamarket.com/data/list/?q=datatype:dataset');
		if($response->getStatusCode() != 200) {
			echo('Impossible d\'obtenir la page : http://datamarket.com/data/list/?q=datatype:dataset');
			return;
		}

		$crawler = new Crawler($response->getContent());
		$node = $crawler->filterXPath('//div[@class="datasets"]/h2');
		if(preg_match("/([0-9,]+)/", $node->text(), $match)) {
			$this->datasetsNumber = (int) str_replace(',', '', $match[0]);
			echo('Number of datasets of the platform : '.$this->datasetsNumber."\n");
		}
		// we now have the number of datasets of the portal.

		// Since we already have the first page, let's parse it !
		$ids      = $crawler->filterXPath('//h4[@class="title"]/a')->extract(array('data-ds'));
		$datasets = array_merge($datasets, $ids);

		// We loop on all pages left.
		//for($i = 2 ; $i < ceil($this->datasetsNumber / $this->batchSize) ; $i++) {
		for($i = 2 ; $i < 5 ; $i++) {
			echo("$i\n");
			$response = $browser->get('http://datamarket.com/data/list/?q=datatype:dataset&page='.$i);
			if($response->getStatusCode() != 200) {
				echo('Impossible d\'obtenir la page n°'.$i);
				continue;
			}

			$crawler  = new Crawler($response->getContent());
			$datasets = array_merge($datasets, $crawler->filterXPath('//h4[@class="title"]/a')->extract(array('data-ds')));
		}
		echo('Nombre de datasets récupérés : '.count($datasets)."\n");
		
		// $datasets contains only the ids of the datasets, we need to add the
		// base url :
		$base_url = $this->base_url;
		$datasets = array_map(
			function($id) use ($base_url) { return($base_url.$id); }
			, $datasets
			);

		return($datasets);
		
	}
    
     public function parsePortal() {
		// TODO
        $this->portal = new \Odalisk\Entity\Portal();
        $this->portal->setName($this->getName());
    }

}
