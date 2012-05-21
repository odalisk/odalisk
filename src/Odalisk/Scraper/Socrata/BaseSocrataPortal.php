<?php

namespace Odalisk\Scraper\Socrata;

use Symfony\Component\DomCrawler\Crawler;

use Odalisk\Scraper\BasePortal;
use Odalisk\Scraper\Tools\RequestDispatcher;

abstract class BaseSocrataPortal extends BasePortal {

    // The base url on which the datasets are listed.
    protected $datasetsListUrl;

    // the number of datasets displayed on one page.
    protected $batch_size;

    public function __construct() {
        $this->criteria = array(
            'setName' => '//h2[@id="datasetName" and @class="clipText currentViewName"]'
            , 'setSummary' => '/p[@class=""]'
            , 'setReleasedOn' => '//span[@class="aboutCreateDate"]/span'
            , 'setSummary' => '//div[@class="aboutDataset"]/div[2]/div/p'
            , 'setLastUpdatedOn' => '//span[@class="aboutUpdateDate"]/span'
            , 'setCategories' => '//div[@class="aboutDataset"]/div[4]/dl/dd[1]'
            //, 'Tags' => '//div[@class="aboutDataset"]/div[4]/dl/dd[3]'
            //, 'Permissions' => '//div[@class="aboutDataset"]/div[4]/dl/dd[2]'
            , 'setProvider' => '//div[@class="aboutDataset"]/div[7]/dl/dd[1]'
            , 'setOwner' => '//div[@class="aboutDataset"]/div[8]/dl/dd[1]/span'
            // , 'Time Period' => '//div[@class="aboutDataset"]/div[8]/dl/dd[2]/span'
            // , 'Frequency' => '//div[@class="aboutDataset"]/div[8]/dl/dd[3]/span'
            // , 'Community Rating' => '//div[@class="aboutDataset"]/div[3]/dl/dd[1]/div'
        );

        $this->urlsListIndexPath = '//td[@class="nameDesc"]/a';
        $this->batch_size = 10;
    }
    
    public function getDatasetsUrls() {
        $dispatcher = new RequestDispatcher($this->buzzOptions, 30);

        $response = $this->buzz->get($this->datasetsListUrl.'1');
        if (200 == $response->getStatusCode()) {
            // We begin by fetching the number of datasets
            $crawler = new Crawler($response->getContent());
            $node = $crawler->filterXPath('//div[@class="resultCount"]');
            if (preg_match("/of ([0-9]+)/", $node->text(), $match)) {
                $this->estimatedDatasetCount = intval($match[1]);
            }
            error_log('[Get URLs] Estimated number of datasets of the portal : ' . $this->estimatedDatasetCount);

            // Since we already have the first page, let's parse it !
            $this->urls = array_merge(
                $this->urls,
                $crawler->filterXPath($this->urlsListIndexPath)->extract(array('href'))
            );

            $request_count = ceil($this->estimatedDatasetCount / $this->batch_size);
            error_log('[Get URLs] Aproximately ' . $request_count . ' requests to do');

            for($i = 2 ; $i <= $request_count ; $i++) {
                $dispatcher->queue(
                    $this->datasetsListUrl.$i,
                    array($this,'Odalisk\Scraper\Socrata\BaseSocrataPortal::crawlDatasetsList')
                );
            }
        }

        $dispatcher->dispatch(10);

        foreach ($this->urls as $key => $id) {
            $this->urls[$key] = $this->getBaseUrl() . $id . '/about';
        }

        $this->totalCount = count($this->urls);

        return $this->urls;
    }

    protected function additionalExtraction($crawler, &$data) 
    {
        $data['setFormat'] = "CSV;JSON;PDF;RDF;RSS;XLS;XLSX;XML";
    }
}

