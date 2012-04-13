<?php 

namespace Odalisk\Scraper\Tools;

use Buzz\Browser;

class RequestDispatcher {
    private $buzz_options = array(
        'User-agent: Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.0.1) Gecko/2008071615 Fedora/3.0.1-1.fc9 Firefox/3.0.1',
    );
    
    public function __construct() {
        $this->client = new MultiCurlAsync();
        $this->client->setTimeout(10);
        $this->buzz = new Browser($this->client);
    }
    
    public function get($url, $options = array()) {
        $this->buzz->get($url, array_merge($this->buzz_options, $options));
    }
    
    public function batchGet(array $urls, $options = array()) {
        foreach($urls as $url) {
            $this->get($url, $options);
        }
    }
    
    public function flush($callback, $window_size = NULL) {
        $this->client->flush($callback, $window_size);
    }
}