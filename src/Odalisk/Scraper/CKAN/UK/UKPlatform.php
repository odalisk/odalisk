<?php

namespace Odalisk\Scraper\CKAN\UK;

use Odalisk\Scraper\CKAN\BaseCKAN;

use Symfony\Component\DomCrawler\Crawler;

/**
 * The scraper for data.gov.uk
 */
class UKPlatform extends BaseCKAN {
    public function __construct() {
        $this->criteria = array(
            'setName' => '//h1[@class="title"]',
            'setSummary' => '//div[@class="package_title"]', 
            'setReleasedOn' => '//td[.="Released" and @class="package_label"]/../td[2]/div[1]'
			, 'setLastUpdatedOn' => '//td[.="Last updated" and @class="package_label"]/../td[2]/div[1]'
			, 'setProvider' => '//td[.="Published by" and @class="package_label"]/../td[2]/div[1]'
			, 'setLicense' => '//td[.="Licence" and @class="package_label"]/../td[2]/div[1]'
            , 'setCategory' => './/*[@class="package_label" and text() = "Categories"]/following-sibling::*'
        );

		$this->date_format = 'Y-m-d';
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
            
            // Post treatment
            // Trim summary
            if(array_key_exists('setSummary', $data)) {
                $data['setSummary'] = trim($data['setSummary']);
            }
            // Trim category
            if(array_key_exists('setCategory', $data)) {
                $data['setCategory'] = trim($data['setCategory']);
            }

            // Normalize licence
            if(array_key_exists('setLicense', $data)) {
                if($data['setLicense'] == '[]') {
                    unset($data['setLicense']);
                } else {
                    if(preg_match('/(OKD Compliant::)?UK Open Government Licence \(OGL\)/', $data['setLicense'])) {
                        $data['setLicense'] = 'OGL';
                    }
                }
            }
            
            if(!array_key_exists('setReleasedOn', $data)) {
                $nodes = $crawler->filterXPath('//*[(@id = "tagline")]');
                
                if(0 < count($nodes)) {
                    $content = trim(join(
                        ";",
                        $nodes->each(
                            function($node,$i) {
                                return $node->nodeValue;
                            }
                        )
                    ));
                    
                    if(preg_match('/^Posted by ([a-zA-Z &,\'-]+) on ([0-9]{2}\/[0-9]{2}\/[0-9]{4})/', $content, $matches)){
                        $data['setProvider'] = $matches[1];
                        $data['setReleasedOn'] = $matches[2];
                    } else {
                        error_log('>>' . $content);
                    }
                }
            }
            
            // We transform dates format in datetime.
			foreach($this->date_fields as $field) {
				if(array_key_exists($field, $data)) {
				    $date = $data[$field];
				    
					if(preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}$/', $date)) {
					    $data[$field] = \Datetime::createFromFormat('Y-m-d H:i', $date);
					} else if (preg_match('/^[0-9]{2}\/[0-9]{2}\/[0-9]{4} [0-9]{2}:[0-9]{2}$/', $date)) {
					    $data[$field] = \Datetime::createFromFormat('d/m/Y H:i', $date);
					} else if(preg_match('/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/', $date)) {
					    $data[$field] = \Datetime::createFromFormat('d/m/Y H:i', $date . ' 00:00');
					} else if(preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $date)) {
                        $data[$field] = \Datetime::createFromFormat('Y-m-d H:i', $date . ' 00:00');
                    } else if(preg_match('/^[0-9]{4}-[0-9]{2}$/', $date)) {
                        $data[$field] = \Datetime::createFromFormat('Y-m-d H:i', $date . '-01 00:00');
                    } else if(preg_match('/^[0-9]{4}$/', $date)) {
                        $data[$field] = \Datetime::createFromFormat('Y-m-d H:i', $date . '-01-01 00:00');
                    } else if($date == 'n/a' || $date == 'TBC') {
                        $data[$field] = NULL;
                    } else {
                        // Not something we recognize
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
    
    public function getDatasetsUrls() {
        // Make the API call
        $response = $this->buzz->get(
            $this->api_url,
            $this->buzz_options
        );
        // Get the paths
        if(200 == $response->getStatusCode()) {
            $data = json_decode($response->getContent());
            
            foreach($data as $key => $dataset_name) {
                $data[$key] = $this->base_url . $dataset_name;
            }
        } else {
            error_log('Couldn\'t fetch list of datasets for ' . $this->name);
        }     
        
        $this->total_count = count($data);
        
        return $data;
    }

	public function parsePortal() {
        $this->portal = new \Odalisk\Entity\Portal();
        $this->portal->setName($this->getName());
        $this->portal->setUrl('http://data.gov.uk/');
        
        $this->em->persist($this->portal);
        $this->em->flush();
	}
}
