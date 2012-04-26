<?php

namespace Odalisk\Scraper\Tools;

use Buzz\Browser;
use Buzz\Message;
use Buzz\Client\Curl;

/**
 * An "asynchronous" cURL client for Buzz
 * 
 * Acknolegdement : This work was inspired by https://github.com/LionsAd/rolling-curl
 */
class MultiCurlAsync extends Curl {
    
    /**
     * The main cURL handle
     */
    private $master;
    
    /**
     * The request queue
     */
    private $queue = array();
    
    /**
     * Maps cURL handles to queue items
     */
    private $request_map = array();
    
    /**
     * Initializes the cURL multi handle
     */
    public function __construct() {
        if(FALSE == $this->master = curl_multi_init()) {
            throw new RuntimeException("Error initializing cURL");
        }
    }
    
    /**
     * Adds a request to the queue. The queue is processed when flush() is called.
     *
     * @param Request $request The request.
     * @param Response $response The response.
     * @return void
     */
    public function queue(Message\Request $request, Message\Response $response, $callback)
    {
        $this->queue[] = array($request, $response, $callback);
    }
    
    /**
     * This processes the request queue. When a request finishes, the callback is called.
     * The callback will be passed the request and the response as arguments (in that order).
     *
     * @param string $callback The callback function that will process the result of the request
     * @param string $window_size How many concurrent requests do we fire?
     * @return void
     */
    public function flush($window_size) {
        // Check that the window size doesn't exceed the number of requests in the queue
        if($window_size > $queue_size = count($this->queue)) {
            $window_size = $queue_size;
        }
        
        // Send the first batch of requests
        for ($i = 0; $i < $window_size; $i++) {
            $this->fireRequest($i);
        }
        
        // Execute the rest as soon as possible
        do {
            $active = NULL;
            do {
                $mrc = curl_multi_exec($this->master, $active);
            } while ($mrc == CURLM_CALL_MULTI_PERFORM);
            
            if($mrc != CURLM_OK)
                break;
            
            // A request was just completed -- find out which one
            while($done = curl_multi_info_read($this->master)) {
                // send the return values to the callback function.
	            $key = (string) $done['handle'];
                list($request, $response, $callback) = $this->queue[$this->request_map[$key]];
                 // Check that the callback is valid, otherwise we work for nothing
                if(is_callable($callback)) {
                    //throw new RuntimeException();
                    unset($this->request_map[$key]);
                
                    $response->fromString(static::getLastResponse(curl_multi_getcontent($done['handle'])));
                                    
                    call_user_func($callback, $request, $response);
                } else {
                    error_log('[RuntimeError] MultiCurlAsync::flush() > The provided callback is not callable (' . $callback . ').');
                }

                // Start a new request (it's important to do this before removing the old one)
                if ($i < count($this->queue) && isset($this->queue[$i])) {
                    $this->fireRequest($i);
                    $i++;
                }

                // Remove the cURL handle that just completed
                curl_multi_remove_handle($this->master, $done['handle']);

            }

	    // Block for data in / output; error handling is done by curl_multi_exec
	    if ($active)
            curl_multi_select($this->master, $this->timeout);

        } while ($active);
        
        curl_multi_close($this->master);
    }
    
    private function fireRequest($i) {
        list($request, $response) = $this->queue[$i];
        $curl = static::createCurlHandle();
        
        $this->prepare($request, $response, $curl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);

        curl_multi_add_handle($this->master, $curl);

        // Add to our request Maps
        $this->request_map[(string) $curl] = $i;
    }
}
