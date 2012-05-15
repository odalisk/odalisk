<?php

namespace Odalisk\Scraper\Socrata;

use Symfony\Component\DomCrawler\Crawler;

use Odalisk\Scraper\BasePlatform;

use Buzz\Message;

abstract class BaseSocrata extends BasePlatform {

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
        // Get the paths
        $this->buzz->getClient()->setTimeout(30);
        $response = $this->buzz->get(
            $this->api_url,
            $this->buzz_options
        );
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
