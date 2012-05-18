<?php

namespace Odalisk\Scraper\DataPublica;

use Symfony\Component\DomCrawler\Crawler;

use Odalisk\Scraper\Tools\RequestDispatcher;

use Odalisk\Scraper\BasePlatform;

use Buzz\Browser;

use Buzz\Message;


use Odalisk\Entity\Dataset;


/**
 * The scraper for in DataPublica
 */
class DataPublicaPlatform extends BasePlatform {

    protected $nb_dataset_estimated = 0;
    
    protected $month_in = array("/janv./", "/févr./", "/mars/", "/avr./", "/mai/", "/juin/", "/juil./", "/août/", "/sept./", "/oct./", "/nov./","/déc./");
    
    protected $month_out = array("01","02","03","04","05","06","07","08","09","10","11","12");

    public function __construct() {

        $this->criteria = array(
            'setName' => ".//*[@id='content']/article[1]/h2",
            'setCategory' => "//div/h5[text()='Catégories']/../following-sibling::*/ul/li/a",
            'setLicense' => "//div/h5[text()='Licence']/../following-sibling::*",
            'setReleasedOn' => "//div/h5[text()='Date de création']/../following-sibling::*",
            'setLastUpdatedOn' => "//div/h5[text()='Date de mise à jour']/../following-sibling::*",
            'setSummary' => ".//*[@id='description']",
            //'setMaintainer' => ".//*[@id='publication_tab_container']/ul/li[1]/div[2]/a",
            'setOwner' => "//div/h5[text()='Editeur']/../following-sibling::*",
        );
        
        $this->datasets_list_url = 'http://www.data-publica.com/search/?page=';
        $this->urls_list_index_path = ".//*[@id='content']/article[2]/ol/li/a";
        $this->date_format = 'd m Y';
    }
    
    public function getDatasetsUrls() {
        $dispatcher = new RequestDispatcher($this->buzz_options, 30);

        $response = $this->buzz->get($this->datasets_list_url.'1');
        if(200 == $response->getStatusCode()) {
            // We begin by fetching the number of datasets
    		$crawler = new Crawler($response->getContent());
    		$nodes = $crawler->filterXPath('.//ul[@class="pagenav"]/li[last()]/a');
    		
    		if(0 < count($nodes)) {
    		    $pages_to_get = intval($nodes->first()->text());

        		// Since we already have the first page, let's parse it !
        		$this->urls = array_merge(
        		    $this->urls,
        		    $crawler->filterXPath($this->urls_list_index_path)->extract(array('href'))
        		);
        		
        		$this->nb_dataset_estimated = count($this->urls) * $pages_to_get;
        		error_log('[Get URLs] Estimated number of datasets of the portal : ' . $this->nb_dataset_estimated);
        		error_log('[Get URLs] Aproximately ' . $pages_to_get . ' requests to do');
        		
        		for($i = 2 ; $i <= $request_count ; $i++) {
        			$dispatcher->queue(
        			    $this->datasets_list_url.$i,
        			    array($this,'Odalisk\Scraper\DataPublica\DataPublicaPlatform::crawlDatasetsList')
        			);
        		}
        		
        		$dispatcher->dispatch(10);
    		}
        }
		
		foreach($this->urls as $key => $id) {
            $this->urls[$key] = $this->base_url . $id;
        }

        $this->total_count = count($this->urls);
		
		return $this->urls;
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
                                return utf8_decode($node->nodeValue);
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
                
            // We transform dates format in datetime.
            foreach($this->date_fields as $field) {

                if(array_key_exists($field, $data)) {
                   $data[$field] = \Datetime::createFromFormat($this->date_format, $this->translateDate($data[$field]));                    
                } else {
                    $data[$field] = NULL;
                }
            }
        }

        $dataset->populate($data);
        $crawler = NULL;
        $data = NULL;
    }
    
    public function translateDate($date){
        return preg_replace($this->month_in , $this->month_out , $date);   
    }
    
    public function parsePortal() {
        $this->portal = new \Odalisk\Entity\Portal();
        $this->portal->setName($this->getName());
        $this->portal->setUrl('http://www.data-publica.com/');
        $this->em->persist($this->portal);
        $this->em->flush();
    }
}
?>
