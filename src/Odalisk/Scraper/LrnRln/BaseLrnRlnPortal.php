<?php

namespace Odalisk\Scraper\LrnRln;

use Symfony\Component\DomCrawler\Crawler;

use Odalisk\Scraper\BasePortal;
use Odalisk\Scraper\Tools\RequestDispatcher;

abstract class BaseLrnRlnPortal extends BasePortal
{
    // The base url on which the datasets are listed.
    protected $datasetsListUrl;

    // the number of datasets displayed on one page.
    protected $batch_size;

    public function __construct()
    {
        $this->criteria = array(
        );

        $this->urlsListIndexPath = '//';
        $this->batch_size = 10;
    }

    public function getDatasetsUrls()
    {
        $dispatcher = new RequestDispatcher($this->buzzOptions, 30);

        $response = $this->buzz->get($this->datasetsListUrl);
        if (200 == $response->getStatusCode()) {
            // We begin by fetching the number of datasets
            $crawler = new Crawler($response->getContent());
            $node = $crawler->filterXPath('//div[@class="infoD"]');
            if (preg_match("/([0-9]+)/", $node->text(), $match)) {
                $this->estimatedDatasetCount = intval($match[0]);
            }
            error_log('[Get URLs] Estimated number of datasets of the portal : ' . $this->estimatedDatasetCount);

            // Now we fetch the datalists thanks to a <select> ;)
            $nodes = $crawler
                ->filterXPath('//[@id="datalist"]//option[@value]')
                ->extract(array('value'))
                ;

            // We construct the urls
            $base_url = $this->getBaseUrl();
            $this->urls = array_map(
                    function($id) {
                        return $base_url.$id;
                    }
        }

        return $this->urls;
    }
}

