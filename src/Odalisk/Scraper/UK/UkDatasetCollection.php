<?php

namespace Odalisk\Scraper\UK;

/**
 * Iterator for Nantes datasets
 */
class UkDatasetCollection implements \Iterator  {
    private $index;
    
    private $paths;
    
    private $paths_count;
    
    private $buzz;
    
    private $base_url;
    
    public function __construct($buzz, $base_url) {
        $this->base_url = $base_url;
        $this->buzz = $buzz;
        $this->index = 0;
        
        // Get the paths
        $this->buzz->getClient()->setTimeout(10);
        $response = $this->buzz->get('http://catalogue.data.gov.uk/api/rest/dataset');
        if(200 == $response->getStatusCode()) {
            $this->paths = json_decode($response->getContent());
            $this->paths_count = count($this->paths);
        } else {
            throw new \RuntimeException('Couldn\'t fetch list of datasets');
        }
    }

    function rewind() {
       $this->index = 0;
    }

    function current() {
        return new UkDataset(
            $this->buzz,
            $this->base_url . $this->paths[$this->index]
        );
    }

    function key() {
        return $this->index;
    }

    function next() {
        ++$this->index;
    }

    function valid() {
        return $this->index < $this->paths_count;
    }
}
