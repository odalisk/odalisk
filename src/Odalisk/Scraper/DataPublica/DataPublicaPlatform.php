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
             
        $this->date_format = 'd m Y';
    }

    public function getDatasetsUrls() {
        $urls = array();

        $path = "src/Odalisk/Scraper/DataPublica/data_publica_urls.txt"; 

        $handle = fopen($path, 'r');
    
        if ($handle)
        {
            for ($ligne = fgetcsv($handle, 1024); !feof($handle); $ligne = fgetcsv($handle, 1024)) {
            $j = sizeof($ligne);
              for ($i = 0; $i < $j; $i++) {
                $urls[] = $ligne[$i];
              }
            }

            fclose($handle);
        }
        
        $this->total_count = count($urls);

        return $urls;
    }


    
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

                        $data[$field] = \Datetime::createFromFormat($this->date_format, $this->translateDate($data[$field]));
                        if(FALSE == $data[$field]) {
                            $data[$field] = NULL;
                        }
                    } else {
                        $data[$field] = NULL;
                    }
                }
            }
            $crawler = NULL;
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
    
    
    public function translateDate($date_in){
        $in = array("/janv./", "/févr./", "/mars/", "/avr./", "/mai/", "/juin/", "/juil./", "/août/", "/nov./","/déc./");
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

}
