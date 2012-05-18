<?php

namespace Odalisk\Scraper\InCiteSolution\LoireAtlantique;

use Odalisk\Scraper\InCiteSolution\BaseInCiteSolution;

use Odalisk\Scraper\Tools\RequestDispatcher;

use Symfony\Component\DomCrawler\Crawler;


/**
 * The scraper for data.loire-atlantique.fr
 */
class LoireAtlantiquePlatform extends BaseInCiteSolution {


    protected $nb_datasets_estimated;


    public function __construct() {

        parent::__construct();

        $this->datasets_list_url = 'http://data.loire-atlantique.fr/donnees/?tx_icsoddatastore_pi1[page]=';

        $this->urls_list_index_path = ".//*[@class='tx_icsoddatastore_pi1_list']//td[@class='first']/h3/a";
    }

    public function getDatasetsUrls() {
        // Create a new Buzz handle, with asynchronous requests
        $dispatcher = new RequestDispatcher($this->buzzOptions, 30);

        // Get the first page
        $response = $this->buzz->get($this->datasets_list_url.'0');

        if ($response->getStatusCode() == 200) {
            $crawler = new Crawler($response->getContent());

            // Try to crawl the paginated website
            $nodes = $crawler->filterXPath(".//div[@class='pagination']/span[@class='last']/a/@href");
            if (0 < count($nodes)) {
                $pages_to_get = 0;

                // Find the number of pages
                $href = $nodes->first()->text();
                if (preg_match("/\[page\]=([0-9]+)&/", $href, $match)) {
                    $pages_to_get = intval($match[1]);
                }

                // Extract URLs from this page
                $nodes = $crawler->filterXPath($this->urls_list_index_path);
                if (0 < count($nodes)) {
                    $this->urls = array_merge($this->urls, $nodes->extract(array('href')));
                    $this->nb_dataset_estimated = count($this->urls);
                }

                // Add requests to the queue
                for($i = 1 ; $i <= $pages_to_get ; $i++) {
                   $this->nb_dataset_estimated += count($this->urls);
                   $dispatcher->queue($this->datasets_list_url.$i,
                        array($this, 'Odalisk\Scraper\InCiteSolution\LoireAtlantique\LoireAtlantiquePlatform::crawlDatasetsList'));
                }

                error_log('[Get URLs] Estimated number of datasets of the portal : ' . $this->nb_dataset_estimated);

                $dispatcher->dispatch(10);

            }
        }

        foreach ($this->urls as $key => $id) {
            $this->urls[$key] = $this->base_url . $id;
        }

        $this->totalCount = count($this->urls);


        return $this->urls;
    }

    public function parsePortal() {
        $this->portal = new \Odalisk\Entity\Portal();
        $this->portal->setName($this->getName());
        $this->portal->setUrl('http://data.loire-atlantique.fr/');

        $this->em->persist($this->portal);
        $this->em->flush();
    }
}
