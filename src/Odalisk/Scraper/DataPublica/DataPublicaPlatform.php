<?php

namespace Odalisk\Scraper\DataPublica;

use Symfony\Component\DomCrawler\Crawler;

use Odalisk\Scraper\Tools\RequestDispatcher;

use Odalisk\Scraper\BasePlatform;

use Buzz\Browser;

use Buzz\Message;

/**
 * The scraper for in DataPublica
 */
class DataPublicaPlatform extends BasePlatform {

    public function __construct() {
        $this->criteria = array(
                          );
             
        $this->date_format = 'd/m/Y';
    }

    public function getDatasetsUrls() {
        
        $categories = array(
                        //"Economie+%26+Finances",
                        "Tourisme+%26+Voyages",
                    );
        
        $browser = new Browser();
        $finished = false;
        $i = 1;

        do{

            foreach($categories as $category){

            

            $response = $browser->get("http://www.data-publica.com/search/?page=".$i);
            $crawler = new Crawler($response->getContent());
            $nodes = $crawler->filterXPath(".//*[@id='content']/article[2]/ol/li/a/@href");
            if(0 < count($nodes)) {
                        $nodes->each(
                            function($node,$i) {
                                  echo $node->nodeValue."\n";
                             }
                          );
                       
            }
            else{
                $finished = true;
            }
            
            $i++;
            echo $response->getStatusCode();
        }while(!$finished);

        // API Call
        $urls = array();
/*        
        $response = $this->buzz->get(
            $this->api_url,
            $this->buzz_options
        );

        if(200 == $response->getStatusCode()) {

            $data = json_decode($response->getContent());
            $factory = new Message\Factory();
                
            foreach($data->opendata->answer->data->dataset as $dataset) {
                $formRequest = $factory->createFormRequest();
                $formRequest->setMethod(Message\Request::METHOD_POST);
                $formRequest->fromUrl($this->sanitize($this->base_url . '?tx_icsoddatastore_pi1[uid]=' . $dataset->id));
                $formRequest->addHeaders($this->buzz_options);
                $formRequest->setFields(array('tx_icsoddatastore_pi1[cgu]' => 'on'));
                $urls[] = $formRequest;
            }
        }  else {
            error_log('Couldn\'t fetch list of datasets for ' . $this->name);
        }     
        
        $this->total_count = count($urls);
 */       
        return $urls;
    }
    
     public function parsePortal() {
        $this->portal = new \Odalisk\Entity\Portal();
        $this->portal->setName($this->getName());
        $this->portal->setUrl('http://www.data-publica.com/');
/*
        $this->em->persist($this->portal);
        $this->em->flush();
*/
    }

}
