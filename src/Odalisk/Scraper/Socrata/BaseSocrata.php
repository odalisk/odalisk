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
			, 'setDescription' => '//div[@class="aboutDataset"]/div[2]/div/p'
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
		$urls    = array(); // the array we will return.
        $this->buzz->getClient()->setTimeout(30);

        $response = $this->buzz->get($this->datasets_list_url.'1');
		if($response->getStatusCode() != 200) {
			echo('Impossible d\'obtenir la page : http://datamarket.com/data/list/?q=datatype:dataset');
			return;
		}

		// We begin by fetching the number of datasets
		$crawler = new Crawler($response->getContent());
		$node = $crawler->filterXPath('//div[@class="resultCount"]');
		if(preg_match("/([0-9]+)/", $node->text(), $match)) {
			$datasets_number = (int) $match[1];
			echo('Number of datasets of the platform : '.$datasets_number."\n");
		}
		// we now have the number of datasets of the portal.

		// Since we already have the first page, let's parse it !
		$ids  =
		$crawler->filterXPath('//tr[@data-viewId]')->extract(array('data-viewId'));
		// Add it to the urls array
		$urls = array_merge($urls, $ids);

        if(200 == $response->getStatusCode()) {
            $data = json_decode($response->getContent());
            foreach($data as $dataset) {
                $datasets[] = $this->base_url.$dataset->id;
            }
        } else {
            throw new \RuntimeException('Couldn\'t fetch list of datasets');
        }     
        
        return($datasets);
	}
}
