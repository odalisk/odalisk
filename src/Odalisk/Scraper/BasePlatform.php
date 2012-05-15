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
	
    protected $count = 0;
    
    protected $total_count = 0;
    
    protected $portal;
    
    protected $datasets;
    
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
    
    /**
     * Load the portal object from the database. If none is found, parse it from the website.
     *
     * @return void
     */
    public function loadPortal() {
        $this->portal = $this->em->getRepository('Odalisk\Entity\Portal')
            ->findOneByName($this->getName());
        
        if(NULL == $this->portal) {
            $this->parsePortal();
        }
    }
    
    /**
     * Fetch the portal from the web, parse it and create a new entity in $this->portal (and persist/flush it)
     *
     * @return void
     */
    abstract public function parsePortal();
    
    /**
     * Load the dataset entities from the database, and create a table indexed by name.
     * This allows us to update datasets rather than recreate them (when parsing)
     *
     * @return void
     */
    public function loadDatasets() {
        if(NULL == $this->portal) {
            $this->loadPortal();
        }
        
        $datasets = $this->portal->getDatasets();
        
        foreach($datasets as $id => $dataset) {
            $this->datasets[$dataset->getUrl()] = $dataset;
            unset($datasets[$id]);
        }
    }
    
    abstract public function getDatasetsUrls();
    
    
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
                
				// We transform dates format in datetime.
				$dateFields = array(
						'setReleasedOn'
						, 'setLastUpdatedOn'
						);
				foreach($dateFields as $field) {
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
        } else {
            $data['setError'] = 'Return code : ' . $response->getStatusCode();
        }

		// Logs
        error_log('[' . $this->name . '] Processed ' . $data['setUrl'] . ' with code ' . $response->getStatusCode());
        if(0 == $this->count % 100) {
           error_log('>>>> ' . $this->count . ' done, ' . $this->total_count . ' to go.');
        }
        
        $dataset = NULL;
        
        if(NULL != $this->datasets && array_key_exists($data['setUrl'], $this->datasets)) {
            $dataset = $this->datasets[$data['setUrl']];
            $dataset->populate($data);
        } else {
            $dataset = new Dataset($data);
            $this->portal->addDataset($dataset);
        }
        
        $this->em->persist($dataset);
        
        if($this->count == $this->total_count || $this->count % 100 == 0) {
            error_log('Flushing data!');
            $this->em->persist($this->portal);
            $this->em->flush();
        }
    }
}
