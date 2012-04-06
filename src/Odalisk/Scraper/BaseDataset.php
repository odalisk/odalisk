<?php

namespace Odalisk\Scraper;

use Symfony\Component\DomCrawler\Crawler;

class BaseDataset {
    protected $buzz;
    
    protected $buzz_options = array();
    
    protected $url;
    
    protected $html = NULL;
    
    protected $criteria = array();
    
    protected $data = array();
    
    public function __construct($buzz, $url) {
        $this->buzz = $buzz;
        $this->url = $url;
    }
    
    public function fetch($timeout = 5) {
        $this->buzz->getClient()->setTimeout($timeout);
        try {
            $response = $this->buzz->get(
                $this->url,
                $this->buzz_options
            );
        } catch (\RuntimeException $e) {
            return FALSE;
        }
        
        if(200 == $response->getStatusCode()) {
            $this->html = $response->getContent();
            return TRUE;
        }
        
        return FALSE;
    }
    
    public function parse() {
        $crawler = new Crawler($this->html);
        if(0 == count($crawler)) {
            return FALSE;
        }
        
        foreach($this->criteria as $name => $path) {
            $node = $crawler->filter($path);
            if(0 != count($node)) {
               $this->data[$name] = $node->text();
            }        
        }
        
        return !empty($this->data);
    }
    
    public function getData() {
        return $this->data;
    }
    
    public function isEmpty() {
        return NULL == $this->html;
    }
}