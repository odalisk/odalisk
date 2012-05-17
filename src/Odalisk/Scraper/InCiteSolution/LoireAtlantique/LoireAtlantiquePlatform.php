<?php

namespace Odalisk\Scraper\InCiteSolution\LoireAtlantique;

use Odalisk\Scraper\InCiteSolution\BaseInCiteSolution;

use Odalisk\Scraper\Tools\RequestDispatcher;

use Symfony\Component\DomCrawler\Crawler;

use Buzz\Message;

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
        
        $this->urllist = array();

        $dispatcher = new RequestDispatcher($this->buzz_options);
        $this->buzz->getClient()->setTimeout(30);

        $response = $this->buzz->get($this->datasets_list_url.'0');

        
        if($response->getStatusCode() == 200) {
            $crawler = new Crawler($response->getContent());
            
            $nodes = $crawler->filterXPath(".//div[@class='pagination']/span[@class='last']/a/@href");
            if(0 < count($nodes)) {

            	$pages_to_get = 0;

            	$href = $nodes->first()->text();
            	if(preg_match("/\[page\]=([0-9]+)&/", $href, $match)) {
					$pages_to_get = intval($match[1]);
				}

				
                
                $nodes = $crawler->filterXPath($this->urls_list_index_path);
                if(0 < count($nodes)) {                           
                    $this->urls = array_merge($this->urls, $nodes->extract(array('href')));
                    $this->nb_dataset_estimated = count($this->urls);
                }

                for($i = 1 ; $i <= $pages_to_get ; $i++) {
                   $this->nb_dataset_estimated += count($this->urls);
                   $dispatcher->queue($this->datasets_list_url.$i,
                                        array($this, 'Odalisk\Scraper\InCiteSolution\LoireAtlantique\LoireAtlantiquePlatform::crawlDatasetsList'));
                }

                error_log('Number estimated of datasets of the portal : '.$this->nb_dataset_estimated);

                $dispatcher->dispatch(10);
                
            }else{
                return $this->urls;
            }

        }
        
        $base_url = $this->base_url;
        $this->urls = array_map(
                function($id) use ($base_url) { return($base_url.$id); }
                , $this->urls
                );

        $this->total_count = count($this->urls);
        
        return $this->urls;
    }

    public function parsePortal() {
        $this->portal = new \Odalisk\Entity\Portal();
        $this->portal->setName($this->getName());
        $this->portal->setUrl('http://data.loire-atlantique.fr/');
        
        $this->em->persist($this->portal);
        $this->em->flush();
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
        if(0 == $count % 10) {
                   error_log('> ' . $count . ' / ' . $this->nb_dataset_estimated . '(estimated) done');
        }
    }
}