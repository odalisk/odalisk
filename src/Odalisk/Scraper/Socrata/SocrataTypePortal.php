<?php

namespace Odalisk\Scraper\Socrata;

use Symfony\Component\DomCrawler\Crawler;

use Odalisk\Scraper\BasePlatform;

use Buzz\Message;

abstract class SocrataTypePortal extends BasePlatform {

	/**
	 * the Portal entity.
	 */
	protected static $portalEntity = NULL;

    private static $i = 0;

    private static $criteria = array(
        'Creation' => '//span[@class="aboutCreateDate"]/span',
        'Description' => '//div[@class="aboutDataset"]/div[2]/div/p',
        'Last update' => '//span[@class="aboutUpdateDate"]/span',
        'Category' => '//div[@class="aboutDataset"]/div[4]/dl/dd[1]',
        'Tags' => '//div[@class="aboutDataset"]/div[4]/dl/dd[3]',
        'Permissions' => '//div[@class="aboutDataset"]/div[4]/dl/dd[2]',
        'Data Provider' => '//div[@class="aboutDataset"]/div[7]/dl/dd[1]',
        'Data Owner' => '//div[@class="aboutDataset"]/div[8]/dl/dd[1]/span',
        'Time Period' => '//div[@class="aboutDataset"]/div[8]/dl/dd[2]/span',
        'Frequency' => '//div[@class="aboutDataset"]/div[8]/dl/dd[3]/span',
        'Community Rating' => '//div[@class="aboutDataset"]/div[3]/dl/dd[1]/div',  
    );

    protected static $datasets = array();

	private static $datasets_number = 18776;
    
    public function __construct($buzz, $base_url, $datasets_api_url) {
        parent::__construct($buzz, $base_url, $datasets_api_url);
    }

    public function getDatasetsData() {
        return self::$datasets;
    }
    
    public function getDatasetsUrls() {
        // Get the paths
        $this->buzz->getClient()->setTimeout(30);
        $response = $this->buzz->get(
            $this->datasets_api_url,
            $this->buzz_options
        );
        if(200 == $response->getStatusCode()) {
            $data = json_decode($response->getContent());
            foreach($data as $dataset) {
                self::$datasets[$this->base_url . $dataset->id] = NULL;
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
                    $node = $crawler->filterXPath($path);
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

	abstract public static function portalEntity();
}
