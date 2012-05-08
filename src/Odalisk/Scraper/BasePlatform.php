<?php

namespace Odalisk\Scraper;

use Symfony\Component\DomCrawler\Crawler;

use Buzz\Message;

use Odalisk\Entity\DataSet;

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
     * The doctrine handle
     *
     * @var string
     */
    protected $doctrine;
    
    /**
     * Entity manager
     *
     * @var string
     */
    protected $em;
    
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
	
	protected $date_format;
	
    protected $count = 0;
    
    protected $total_count = 0;
    
    public function setBuzz(\Buzz\Browser $buzz, $timeout = 30) {
        $this->buzz = $buzz;
        $this->buzz->getClient()->setTimeout($timeout);
    }
    
    public function setBuzzOptions(array $options) {
        $this->buzz_options = $options;
    }
    
    public function setDoctrine($doctrine) {
        $this->doctrine = $doctrine;
        $this->em = $this->doctrine->getEntityManager();
    }
    
    public function setName($name) {
        $this->name = $name;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function setBaseUrl($base_url) {
        $this->base_url = $base_url;
    }
    
    public function setApiUrl($api_url) {
        $this->api_url = $api_url;
    }
    
    abstract public function getDatasetsUrls();
    
    public function parseDataset(Message\Request $request, Message\Response $response) {     
        $this->count++;
        $data = array(
            'setUrl' => $request->getUrl(),
            // 'code' => $response->getStatusCode(),
        );

        if(200 == $response->getStatusCode()) {
            $crawler = new Crawler($response->getContent());
            if(0 == count($crawler)) {
                $data['setError'] = "Empty page";
            } else {
                foreach($this->criteria as $name => $path) {
                    $nodes = $crawler->filterXPath($path);
                    if(0 < count($nodes)) {
                        $data[$name] = join(
                            ";",
                            $nodes->each(
                                function($node,$i) {
                                    return $node->nodeValue;
                                }
                            )
                        );
                    } 
                }
                
                if(array_key_exists('setReleasedOn', $data)) {
                    $data['setReleasedOn'] = \Datetime::createFromFormat($this->date_format, $data['setReleasedOn']);
                }
                if(array_key_exists('setLastUpdatedOn', $data)) {
                    $data['setLastUpdatedOn'] = \Datetime::createFromFormat($this->date_format, $data['setLastUpdatedOn']);
                }
            }
        }
        error_log('[' . $this->name . '] Processed ' . $data['setUrl'] . ' with code ' . $response->getStatusCode());
        
        if(0 == $this->count % 100) {
           error_log('>>>> ' . $this->count . ' done, ' . $this->total_count . ' to go.');
        }
        
        if(!array_key_exists('setName', $data)) {
           $data['setError'] = "Empty title";
        }
        
        $this->em->persist(new DataSet($data));
    }
}
