<?php

namespace Odalisk\Scraper\DataPublica;

use Symfony\Component\DomCrawler\Crawler;

use Odalisk\Scraper\Tools\RequestDispatcher;

use Odalisk\Scraper\BasePlatform;

/**
 * The scraper for in DataPublica
 */
class DataPublicaPlatform extends BasePlatform
{

    protected $estimatedDatasetCount = 0;

    protected $monthText = array("/janv./", "/févr./", "/mars/", "/avr./", "/mai/", "/juin/", "/juil./", "/août/", "/sept./", "/oct./", "/nov./","/déc./");

    protected $monthNumber = array("01","02","03","04","05","06","07","08","09","10","11","12");

    public function __construct() 
    {
        $this->criteria = array(
            'setName' => ".//*[@id='content']/article[1]/h2",
            'setCategory' => "//div/h5[text()='Catégories']/../following-sibling::*/ul/li/a",
            'setLicense' => "//div/h5[text()='Licence']/../following-sibling::*",
            'setReleasedOn' => "//div/h5[text()='Date de création']/../following-sibling::*",
            'setLastUpdatedOn' => "//div/h5[text()='Date de mise à jour']/../following-sibling::*",
            'setSummary' => ".//*[@id='description']",
            //'setMaintainer' => ".//*[@id='publication_tab_container']/ul/li[1]/div[2]/a",
            'setOwner' => "//div/h5[text()='Editeur']/../following-sibling::*",
            'setFormat' => './/*[@class="format"]/li',
        );

        $this->datasetsListUrl = 'http://www.data-publica.com/search/?page=';
        $this->urlsListIndexPath = ".//*[@id='content']/article[2]/ol/li/a";
        $this->dateFormat = 'd m Y';
    }

    public function getDatasetsUrls()
    {
        $dispatcher = new RequestDispatcher($this->buzzOptions, 30);

        $response = $this->buzz->get($this->datasetsListUrl.'1');
        if (200 == $response->getStatusCode()) {
            // We begin by fetching the number of datasets
            $crawler = new Crawler($response->getContent());
            $nodes = $crawler->filterXPath('.//ul[@class="pagenav"]/li[last()]/a');

            if (0 < count($nodes)) {
                $pages_to_get = intval($nodes->first()->text());

                // Since we already have the first page, let's parse it !
                $this->urls = array_merge(
                    $this->urls,
                    $crawler->filterXPath($this->urlsListIndexPath)->extract(array('href'))
                );

                $this->estimatedDatasetCount = count($this->urls) * $pages_to_get;
                error_log('[Get URLs] Estimated number of datasets of the portal : ' . $this->estimatedDatasetCount);
                error_log('[Get URLs] Aproximately ' . $pages_to_get . ' requests to do');

                for($i = 2 ; $i <= $pages_to_get ; $i++) {
                    $dispatcher->queue(
                        $this->datasetsListUrl.$i,
                        array($this,'Odalisk\Scraper\DataPublica\DataPublicaPlatform::crawlDatasetsList')
                    );
                }

                $dispatcher->dispatch(10);
            }
        }

        foreach ($this->urls as $key => $id) {
            $this->urls[$key] = $this->base_url . $id;
        }

        $this->totalCount = count($this->urls);


        return $this->urls;
    }
    
    protected function additionalExtraction($crawler, &$data) 
    {
        // Deal with UTF8
        foreach($data as $key => $value) {
            $data[$key] = utf8_decode($value);
        }
        
        // Convert dates to known format
        foreach($this->dateFields as $field) {
            $data[$field] = $this->translateDate($data[$field]);
        }
    }

    protected function additionalNormalization(&$data)
    {
        if (array_key_exists('setCategory', $data)) {
            $data['setCategory'] = str_replace(';de;', ' de ', $data['setCategory']);
        }
    }

    public function translateDate($date){
        return preg_replace($this->monthText , $this->monthNumber , $date);
    }

    public function parsePortal() {
        $this->portal = new \Odalisk\Entity\Portal();
        $this->portal->setName($this->getName());
        $this->portal->setUrl($this->getBaseUrl());
        $this->portal->setCountry($this->country);
        $this->portal->setStatus($this->status);
        $this->portal->setEntity($this->entity);
        $this->em->persist($this->portal);
        $this->em->flush();
    }
}
