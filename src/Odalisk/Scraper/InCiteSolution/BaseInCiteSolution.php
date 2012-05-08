<?php

namespace Odalisk\Scraper\InCiteSolution;

use Symfony\Component\DomCrawler\Crawler;

use Odalisk\Scraper\Tools\RequestDispatcher;

use Odalisk\Scraper\BasePlatform;

use Buzz\Message;

/**
 * The scraper for in cite Solution Plateform
 */
abstract class BaseInCiteSolution extends BasePlatform {

    public $i = 0;

    public function __construct() {
        $this->criteria = array(
            'Title' => ".//*[@class='tx_icsoddatastore_pi1_single']/h1",
            'Category' => ".//*[@class='tx_icsoddatastore_pi1_categories separator']/span[@class='value']",
            'Licence' => ".//*[@class='tx_icsoddatastore_pi1_licence separator']/span[@class='value']",
            'Update Frequency' => ".//*[@class='tx_icsoddatastore_pi1_updatefrequency separator']/span[@class='value']",
            'Date of publication' => ".//*[@class='tx_icsoddatastore_pi1_releasedate separator']/span[@class='value']",
            'Last update' => ".//*[@class='tx_icsoddatastore_pi1_updatedate separator']/span[@class='value']",
            'Description' => ".//*[@class='tx_icsoddatastore_pi1_description separator']/span[@class='value']",
            'Manager' => ".//*[@class='tx_icsoddatastore_pi1_manager separator']/span[@class='value']",
            'Last update' => ".//*[@class='tx_icsoddatastore_pi1_owner separator']/span[@class='value']",
            'Technical data' => ".//*[@class='tx_icsoddatastore_pi1_technical_data separator']/span[@class='value']",
            'Formats' => ".//*[@class='tx_icsoddatastore_pi1_file']/a/img/@alt",
             );
    }
    
    public function getDatasetsUrls() {
        
        // API Call
        $urls = array();
        
        $this->buzz->getClient()->setTimeout(10);

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
        }
        else
        {
            error_log('Couldn\'t fetch list of datasets for ' . $this->name);
        }     
        
        $this->total_count = count($urls);
        
        return $urls;
    }

    
    public function parseDataset(Message\Request $request, Message\Response $response){

        
        $datasets = array();
     
        $data = array(
            '#' => $this->i++,
            'url' => $request->getUrl(),
            'code' => $response->getStatusCode(),
        );

        if(200 == $data['code']) {

            $crawler = new Crawler($response->getContent());

            if(0 == count($crawler)) {
                $data['empty'] = TRUE;
            } else {

                foreach($this->criteria as $name => $path) {
                    $nodes = $crawler->filterXPath($path);
                    if(0 != count($nodes)) {

                      $data[$name] = join(";",$nodes->each(function($node,$i)
                                        {
                                            return $node->nodeValue;
                                        }
                                        ));
                    } 

                }
                
            }
        }

        if(!empty($data['Title'])){
            $datasets[$data['url']] = $data;
        }

        if(0 == $this->i % 10) {
           error_log('>>>> ' . $this->i . ' done, ' . count($datasets) . ' to go.');
        }
    }
    
    public function sanitize($url) {
        return str_replace(']', '%5D', str_replace('[', '%5B', $url));
    }
}
