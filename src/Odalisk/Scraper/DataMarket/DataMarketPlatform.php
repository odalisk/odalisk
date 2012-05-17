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
	private $datasets_list_url = 'http://datamarket.com/data/list/?q=datatype:dataset';
	// the number of datasets displayed for a request.
	private $batch_size = 20;

    public function __construct() {
        $this->criteria = array(
				'setName' => '//div[@id="dataset-info"]/h1'
				, 'setLicense' => '//strong[.="Licenses:"]/ul/li/p'
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
		$urls    = array(); // the array we will return.

		// The number of datasets of the portal ; information given on
		// $dataset_list_url page.
		$datasets_number = 0;
		
		// We get the page with the datasets list
        $this->buzz->getClient()->setTimeout(30);
		$response = $this->buzz->get($this->datasets_list_url);
		if($response->getStatusCode() != 200) {
			echo('Impossible d\'obtenir la page : http://datamarket.com/data/list/?q=datatype:dataset');
			return;
		}

		// We begin by fetching the number of datasets
		$crawler = new Crawler($response->getContent());
		$node = $crawler->filterXPath('//div[@class="datasets"]/h2');
		if(preg_match("/([0-9,]+)/", $node->text(), $match)) {
			$datasets_number = (int) str_replace(',', '', $match[0]);
			echo('Number of datasets of the platform : '.$datasets_number."\n");
		}
		// we now have the number of datasets of the portal.

		// Since we already have the first page, let's parse it !
		$ids  = $crawler->filterXPath('//h4[@class="title"]/a')->extract(array('data-ds'));
		// Add it to the urls array
		$urls = array_merge($urls, $ids);

		// $max = ceil($this->datasets_number / $this->batch_size);
		$max = 5;
		for($i = 2 ; $i < $max ; $i++) {
			// We loop on all pages left.
			echo("$i\n");
			$response = $this->buzz->get('http://datamarket.com/data/list/?q=datatype:dataset&page='.$i);
			if($response->getStatusCode() != 200) {
				echo('Impossible d\'obtenir la page n°'.$i);
				continue;
			}

			$crawler = new Crawler($response->getContent());
			$urls    = array_merge($urls, $crawler->filterXPath('//h4[@class="title"]/a')->extract(array('data-ds')));
		}
        $this->total_count = count($urls);
		echo('Nombre de datasets récupérés : '.$this->total_count."\n");
		
		// $urls contains only the ids of the datasets, we need to add the
		// base url :
		$base_url = $this->base_url;
		$urls= array_map(
			function($id) use ($base_url) { return($base_url.$id); }
			, $urls
			);

		return($urls);
		
	}
    
     public function parsePortal() {
        $this->portal = new \Odalisk\Entity\Portal();
        $this->portal->setName($this->getName());
        $this->portal->setUrl('http://datamarket.com/');
        
        $this->em->persist($this->portal);
        $this->em->flush();
    }

}
