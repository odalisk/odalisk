<?php

namespace Odalisk\Scraper\Nantes;

use Symfony\Component\DomCrawler\Crawler;

use Odalisk\Scraper\BasePortal;

use Buzz\Message;

/**
 * The scraper for data.nantes.fr
 */
class NantesPortal extends BasePortal {
    private $datasets_api_url = 'http://data.nantes.fr/api/datastore_searchdatasets/1.0/39W9VSNCSASEOGV/?output=json';
    
    private static $criteria = array(
        'Category' => '.tx_icsoddatastore_pi1_categories > span.value',
        'Licence' => '.tx_icsoddatastore_pi1_licence > span.value',
        'Update Frequency' => '.tx_icsoddatastore_pi1_updatefrequency > span.value',
        "Date of publication" => '.tx_icsoddatastore_pi1_releasedate > span.value',
        "Last update" => '.tx_icsoddatastore_pi1_updatedate > span.value',
        "Description" => '.tx_icsoddatastore_pi1_description > span.value',
        'Manager' => '.tx_icsoddatastore_pi1_manager > span.value',
        'Owner' => '.tx_icsoddatastore_pi1_owner > span.value',
        "Technical data" => '.tx_icsoddatastore_pi1_technical_data > span.value',
    );
    
    private static $datasets = array();
    
    private static $i = 0;
    
    public function __construct($buzz) {
        parent::__construct($buzz, 'http://data.nantes.fr/donnees/detail/');
    }
    
    public function getDatasetsData() {
        return self::$datasets;
    }
    
    public function getDatasetsUrls() {
        // Get the paths
        $this->buzz->getClient()->setTimeout(10);
        $response = $this->buzz->get(
            $this->datasets_api_url,
            $this->buzz_options
        );
        if(200 == $response->getStatusCode()) {
            $data = json_decode($response->getContent());
            foreach($data->opendata->answer->data->dataset as $dataset) {
                self::$datasets[$this->sanitize($this->base_url . '?tx_icsoddatastore_pi1[uid]=' . $dataset->id)] = NULL;
            }
        } else {
            throw new \RuntimeException('Couldn\'t fetch list of datasets');
        }      
        
        return array_keys(self::$datasets);
    }
    
    public static function parseDataset(Message\Request $request, Message\Response $response) {
        $data = array(
            '#' => self::$i++,
            'url' => $request->getUrl(),
            'code' => $response->getStatusCode(),
        );
        
        if(200 == $data['code']) {
            $crawler = new Crawler($response->getContent());
            if(0 == count($crawler)) {
                $data['empty'] = TRUE;
            } else {
                foreach(self::$criteria as $name => $path) {
                    $node = $crawler->filter($path);
                    if(0 != count($node)) {
                       $data[$name] = $node->text();
                    }        
                }
            }
        }
        
        self::$datasets[$data['url']] = $data;
        
        if(0 == self::$i % 100) {
           error_log('>>>> ' . self::$i . ' done, ' . count(self::$datasets) . ' to go.');
        }
    }
    
    public function sanitize($url) {
        return str_replace(']', '%5D', str_replace('[', '%5B', $url));
    }
}
