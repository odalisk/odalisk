<?php

namespace Odalisk\Scraper\Socrata;

use Symfony\Component\DomCrawler\Crawler;

use Odalisk\Scraper\BasePlatform;

use Buzz\Message;

abstract class BaseSocrata extends BasePlatform {

	// The base url on which the datasets are listed.
	private $datasets_list_url = 'https://opendata.socrata.com/browse?&page=';
	// the number of datasets displayed on one page.
	private $batch_size = 10;

	public function __construct() {
		$this->criteria = array(
			'setName' => '//h2[@id="datasetName" and @class="clipText currentViewName"]'
			, 'setSummary' => '/p[@class=""]'
			, 'setReleasedOn' => '//span[@class="aboutCreateDate"]/span'
			, 'setSummary' => '//div[@class="aboutDataset"]/div[2]/div/p'
			, 'setLastUpdatedOn' => '//span[@class="aboutUpdateDate"]/span'
			, 'setCategory' => '//div[@class="aboutDataset"]/div[4]/dl/dd[1]'
			//, 'Tags' => '//div[@class="aboutDataset"]/div[4]/dl/dd[3]'
			//, 'Permissions' => '//div[@class="aboutDataset"]/div[4]/dl/dd[2]'
			, 'setProvider' => '//div[@class="aboutDataset"]/div[7]/dl/dd[1]'
			, 'setOwner' => '//div[@class="aboutDataset"]/div[8]/dl/dd[1]/span'
			// , 'Time Period' => '//div[@class="aboutDataset"]/div[8]/dl/dd[2]/span'
			// , 'Frequency' => '//div[@class="aboutDataset"]/div[8]/dl/dd[3]/span'
			// , 'Community Rating' => '//div[@class="aboutDataset"]/div[3]/dl/dd[1]/div'
		);

        $this->date_format = 'M d, Y';
	}

    public function getDatasetsUrls() {
		$urls = array(); // the array we will return.
        $this->buzz->getClient()->setTimeout(30);

        $response = $this->buzz->get($this->datasets_list_url.'1');
		if($response->getStatusCode() != 200) {
			echo('Impossible d\'obtenir la page  !');
			return;
		}

		// We begin by fetching the number of datasets
		$crawler = new Crawler($response->getContent());
		$node    = $crawler->filterXPath('//div[@class="resultCount"]');
		if(preg_match("/of ([0-9]+)/", $node->text(), $match)) {
			$datasets_number = (int) $match[1];
			echo('Number of datasets of the platform : '.$datasets_number."\n");
		}
		// we now have the number of datasets of the portal.

		// Since we already have the first page, let's parse it !
		$ids  = $crawler->filterXPath('//td[@class="nameDesc"]/a')->extract(array('href'));
		// Add it to the urls array
		$urls = array_merge($urls, $ids);

		// $max = ceil($this->datasets_number / $this->batch_size);
		$max = 5;
		for($i = 2 ; $i < $max ; $i++) {
			// We loop on all pages left.
			echo("$i\n");
			$response = $this->buzz->get($this->datasets_list_url.$i);
			if($response->getStatusCode() != 200) {
				echo('Impossible d\'obtenir la page n°'.$i);
				continue;
			}

			$crawler = new Crawler($response->getContent());
			$urls    = array_merge($urls, $crawler->filterXPath('//td[@class="nameDesc"]/a')->extract(array('href')));
		}
        $this->total_count = count($urls);
		echo('Nombre de datasets récupérés : '.$this->total_count."\n");
		
		// $urls contains only the ids of the datasets, we need to add the
		// base url :
		$base_url = $this->base_url;
		$urls = array_map(
				function($id) use ($base_url) { return($base_url.$id); }
				, $urls
				);

		return($urls);
	}
}
