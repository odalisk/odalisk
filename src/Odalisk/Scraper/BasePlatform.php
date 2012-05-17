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
    
    public function parseFile($html, &$dataset) {
        $crawler = new Crawler($html);
        $data = array();
        
        if(0 != count($crawler)) {
            $data = $this->analysePage($crawler);
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
    
    public function analysePage($crawler) {
        $data = array();
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
        return $data;
    }
    
    
    /**
     * Parse and persist a dataset
     *
     * @param Request $request 
     * @param Response $response 
     * @return void
     */
    public function parseDataset(Message\Request $request, Message\Response $response) {
        $this->count++;
        $data = array(
            'setUrl' => $request->getUrl(),
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
            $crawler = NULL;
        } else {
            $data['setError'] = 'Return code : ' . $response->getStatusCode();
        }

		// Logs
        // error_log('[' . $this->name . '] Processed ' . $data['setUrl'] . ' with code ' . $response->getStatusCode());
        if(0 == $this->count % 100) {
           error_log('> ' . $this->count . ' / ' . $this->total_count . ' done');
           error_log('> ' . memory_get_usage(true) / (8 * 1024 * 1024));
        }
            
        $dataset = new Dataset($data);
        $this->portal->addDataset($dataset);
        $this->em->persist($dataset);

        if($this->count == $this->total_count || $this->count % 1000 == 0) {
            error_log('Flushing data!');
            $this->em->persist($this->portal);
            $this->em->flush();
        }
    }
}
