<?php

namespace Odalisk\Scraper\UK;

use Symfony\Component\DomCrawler\Crawler;

use Odalisk\Scraper\BasePortal;

use Buzz\Message;

/**
 * The scraper for data.nantes.fr
 */
class UkPortal extends BasePortal {
    protected $datasets_api_url = 'http://catalogue.data.gov.uk/api/rest/dataset';
    
    private static $criteria = array(
        'name' => 'h1.title',
    );
    
    private static $datasets = array();
    
    private static $i = 0;
    
    public function __construct($buzz) {
        parent::__construct($buzz, 'http://data.gov.uk/dataset/');
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
        // Get the paths
        if(200 == $response->getStatusCode()) {
            $data = json_decode($response->getContent());
            foreach($data as $dataset) {
                self::$datasets[$this->base_url . $dataset] = NULL;
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
    
    public function removeDataset($dataset) {
        unset(self::$datasets[$dataset]);
    }
}
