<?php

namespace Odalisk\Scraper\Nantes;

/**
 * Iterator for Nantes datasets
 */
class NantesDatasetCollection implements \Iterator  {
    private $index;
    
    private $buzz;
    
    private $base_url;
    
    public function __construct($buzz, $base_url) {
        $this->base_url = $base_url;
        $this->buzz = $buzz;
        $this->index = 14;
    }

    function rewind() {
       $this->index = 14;
    }

    function current() {
        return new NantesDataset(
            $this->buzz,
            $this->sanitize($this->base_url . '?tx_icsoddatastore_pi1[uid]=' . $this->index)
        );
    }

    function key() {
        return $this->index;
    }

    function next() {
        ++$this->index;
    }

    function valid() {
        return $this->index <= 66;
    }
    
    public function sanitize($url) {
        return str_replace(']', '%5D', str_replace('[', '%5B', $url));
    } 
}
