<?php

namespace Odalisk\Scraper;

use Symfony\Component\DomCrawler\Crawler;

use Buzz\Message;

abstract class BasePlatform {
    /**
     * Buzz instance
     *
     * @var Buzz\Browser
     */
    protected $buzz;
    
    /**
     * Default options for Buzz
     *
     * @var array
     */
    protected $buzz_options = array();
    
    /**
     * The name of the current platform
     *
     * @var string
     */
    protected $name;
    
	/**
	 * The base of a dataset url.
	 * 
	 * @var string
	 */
    protected $base_url;

	/**
	 * The api url that retrieves urls of all the datasets of the platform.
	 * 
	 * @var string
	 */
	protected $api_url;
	
	
	protected $criteria;
	
    protected $count = 0;
    
    protected $total_count = 0;
    
    public function setBuzz(\Buzz\Browser $buzz) {
        $this->buzz = $buzz;
    }
    
    public function setBuzzOptions(array $options) {
        $this->buzz_options = $options;
    }
    
    public function setName($name) {
        $this->name = $name;
    }
    
    public function setBaseUrl($base_url) {
        $this->base_url = $base_url;
    }
    
    public function setApiUrl($api_url) {
        $this->api_url = $api_url;
    }
    
    abstract public function getDatasetsUrls();
    
    public function parseDataset(Message\Request $request, Message\Response $response) {
        $data = array(
            '#' => $this->count++,
            'url' => $request->getUrl(),
            'code' => $response->getStatusCode(),
        );
        
        if(200 == $data['code']) {
            $crawler = new Crawler($response->getContent());
            if(0 == count($crawler)) {
                $data['empty'] = TRUE;
            } else {
                foreach($this->criteria as $name => $path) {
                    $node = $crawler->filter($path);
                    if(0 != count($node)) {
                       $data[$name] = $node->text();
                    }        
                }
            }
        }
        
        //$this->datasets[$data['url']] = $data;
        error_log('[' . $this->name . '] Processed ' . $data['url'] . ' with code ' . $data['code']);
        
        if(0 == $this->count % 100) {
           error_log('>>>> ' . $this->count . ' done, ' . $this->total_count . ' to go.');
        }
    }
}
