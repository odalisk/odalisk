<?php

namespace Odalisk\Scraper\Socrata;

use Symfony\Component\DomCrawler\Crawler;
use Buzz\Message;

use Odalisk\Scraper\BasePlatform;
use Odalisk\Scraper\Tools\RequestDispatcher;

abstract class BaseSocrata extends BasePlatform {

	// The base url on which the datasets are listed.
	protected $datasets_list_url;

	// the number of datasets displayed on one page.
	protected $batch_size;

	// The counter of the number of finished requests.
	protected $i_requests = 0;


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
        $this->urls_list_index_path = '//td[@class="nameDesc"]/a';
		$this->batch_size = 10;
	}


	
    public function getDatasetsUrls() {
        $dispatcher = new RequestDispatcher(array());
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
			$this->nb_dataset_estimated = (int) $match[1];
		}

		error_log('Number of datasets of the portal : '.$this->nb_dataset_estimated);
		// we now have the number of datasets of the portal.

		// Since we already have the first page, let's parse it !
		$ids  = $crawler->filterXPath($this->urls_list_index_path)->extract(array('href'));
		// Add it to the urls array
		$this->urls = array_merge($this->urls, $ids);
		

		$max = ceil($this->nb_dataset_estimated / $this->batch_size);
		error_log($max.' requests to do');
		// $max = 5;
		for($i = 2 ; $i <= $max ; $i++) {
			// We add all pages left.
			$dispatcher->queue($this->datasets_list_url.$i, array($this,'Odalisk\Scraper\Socrata\BaseSocrata::crawlDatasetsList'));
			// error_log($i.' / '.$max);

		}
		$dispatcher->dispatch(10);

        $this->total_count = count($this->urls);
		
		// $urls contains only the ids of the datasets, we need to add the
		// base url :
		$base_url = $this->base_url;
		$this->urls = array_map(
				function($id) use ($base_url) { return($base_url.$id); }
				, $this->urls
				);

		return($this->urls);
	}
	

    public function crawlDatasetsList(Message\Request $request, Message\Response $response) {
        
        if($response->getStatusCode() != 200) {
            error_log('Impossible d\'obtenir la page !');
            return;
        }

        $crawler = new Crawler($response->getContent());
        $nodes = $crawler->filterXPath($this->urls_list_index_path);
        if(0 < count($nodes)) {                           
            $this->urls = array_merge($this->urls, $nodes->extract(array('href')));
        }

        $count = count($this->urls);
        if(0 == $count % 100) {
                   error_log('> ' . $count. ' / ' . $this->nb_dataset_estimated . ' done');
        }
    }
}
