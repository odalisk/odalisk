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
            
            // We transform dates format in datetime.
			foreach($this->date_fields as $field) {
				if(array_key_exists($field, $data)) {
					if(preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}$/', $data[$field])) {
					    $data[$field] = \Datetime::createFromFormat('Y-m-d H:i', $data[$field]);
					} else if (preg_match('/^[0-9]{2}\/[0-9]{2}\/[0-9]{4} [0-9]{2}:[0-9]{2}$/', $data[$field])) {
					    $data[$field] = \Datetime::createFromFormat('d/m/Y H:i', $data[$field]);
					} else if(preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $data[$field])) {
                        $data[$field] = \Datetime::createFromFormat('Y-m-d', $data[$field]);
                    } else if(preg_match('/^[0-9]{4}-[0-9]{2}$/', $data[$field])) {
                        $data[$field] = \Datetime::createFromFormat('Y-m', $data[$field]);
                    } else if(preg_match('/^[0-9]{4}$/', $data[$field])) {
                        $data[$field] = \Datetime::createFromFormat('Y', $data[$field]);
                    } else if($data[$field] == 'n/a'){
                        $data[$field] = NULL;
                    } else {
                        var_dump($data);
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
