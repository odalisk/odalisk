<?php

namespace Odalisk\Scraper;

use Symfony\Component\DomCrawler\Crawler;

use Buzz\Message;

use Odalisk\Entity\Dataset;

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
	
	protected $date_fields = array('setReleasedOn', 'setLastUpdatedOn');
	
    protected $count = 0;
    
    protected $total_count = 0;
    
    protected $portal;
	
	/**
	 * Le tableau qui contient les urls des datasets.
	 */
	protected $urls = array();
    
    protected $urls_list_index_path;


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
    
    public function getBaseUrl() {
        return $this->base_url;
    }
    
    public function setApiUrl($api_url) {
        $this->api_url = $api_url;
    }
    
    public function getCount() {
        return $this->total_count;
    }
    
    /**
     * Load the portal object from the database. If none is found, parse it from the website.
     *
     * @return Portal
     */
    public function loadPortal() {
        $this->portal = $this->em->getRepository('Odalisk\Entity\Portal')
            ->findOneByName($this->getName());
        
        if(NULL == $this->portal) {
            $this->parsePortal();
        }
        
        return $this->portal;
    }
    
    public function getPortal() {
        return $this->portal;
    }
    
    /**
     * Fetch the portal from the web, parse it and create a new entity in $this->portal (and persist/flush it)
     *
     * @return void
     */
    abstract public function parsePortal();
    
    abstract public function getDatasetsUrls();
    
    public function prepareRequestsFromUrls($urls) {
        return $urls;
    }
    
    public function parseFile($html, &$dataset) {
        $crawler = new Crawler($html);
        $data = array();
        
        if(0 != count($crawler)) {
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
            // We transform dates format in datetime.
			foreach($this->date_fields as $field) {
				if(array_key_exists($field, $data)) {
					$data[$field] = \Datetime::createFromFormat($this->date_format, $data[$field]);
					if(FALSE == $data[$field]) {
						$data[$field] = NULL;
					}
				} else {
					$data[$field] = NULL;
				}
			}
        }
        
        $dataset->populate($data);
        $crawler = NULL;
        $data = NULL;
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

        $count = count($this->urllist);
        if(0 == $count % 100) {
                   error_log('> ' . $count . ' / ' . $this->nb_dataset_estimated . ' done');
        }
    }
}
