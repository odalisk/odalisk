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
        $this->date_format = 'd m Y';
    }

    public function getDatasetsUrls() {
        

        $this->urls = array();

        $dispatcher = new RequestDispatcher($this->buzz_options);
        $this->buzz->getClient()->setTimeout(30);

        $response = $this->buzz->get($this->datasets_list_url.'1');

        
        if($response->getStatusCode() == 200) {
            $crawler = new Crawler($response->getContent());
            
            $nodes = $crawler->filterXPath('.//ul[@class="pagenav"]/li[last()]/a');
            if(0 < count($nodes)) {

                $pages_to_get = intval($nodes->first()->text());
                
                $nodes = $crawler->filterXPath(".//*[@id='content']/article[2]/ol/li/a");
                if(0 < count($nodes)) {                           
                    $this->urls = array_merge($this->urls, $nodes->extract(array('href')));
                    $this->nb_dataset_estimated = count($this->urls) * $pages_to_get;
                }

                for($i = 2 ; $i <= $pages_to_get ; $i++) {
                   $dispatcher->queue($this->datasets_list_url.$i,
                                        array($this, 'Odalisk\Scraper\DataPublica\DataPublicaPlatform::crawlDatasetsList'));
                }

                error_log('Number estimated of datasets of the portal : '.$this->nb_dataset_estimated);

                $dispatcher->dispatch(10);
                
            }else{
                return $this->urls;
            }

        }
        
        $base_url = $this->base_url;
        $this->urls = array_map(
                function($id) use ($base_url) { return($base_url.$id); }
                , $this->urls
                );

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
    
    public function translateDate($date_in){

        $in = array("/janv./", "/févr./", "/mars/", "/avr./", "/mai/", "/juin/", "/juil./", "/août/", "/sept./", "/oct./", "/nov./","/déc./");
        $out = array("01","02","03","04","05","06","07","08","09","10","11","12");

        return preg_replace($in,$out,$date_in);   
    }
    
    public function parsePortal() {
        $this->portal = new \Odalisk\Entity\Portal();
        $this->portal->setName($this->getName());
        $this->portal->setUrl('http://www.data-publica.com/');
        $this->em->persist($this->portal);
        $this->em->flush();
    }

    public function crawlDatasetsList(Message\Request $request, Message\Response $response) {
        
        if($response->getStatusCode() != 200) {
            error_log('Impossible d\'obtenir la page !');
            return;
        }

        $crawler = new Crawler($response->getContent());
        $nodes = $crawler->filterXPath(".//*[@id='content']/article[2]/ol/li/a");
        if(0 < count($nodes)) {                           
            $this->urls = array_merge($this->urls, $nodes->extract(array('href')));
        }

        $count = count($this->urls);
        if(0 == $count % 100) {
                   error_log('> ' . $count . ' / ' . $this->nb_dataset_estimated . '(estimated) done');
        }
    }
}
?>
