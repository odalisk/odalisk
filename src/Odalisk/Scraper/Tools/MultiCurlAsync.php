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
                error_log('Collecting 1 > ' . memory_get_usage(true) / 1024);
                // send the return values to the callback function.
	            $key = (string) $done['handle'];
	            error_log('Collecting 2 > ' . memory_get_usage(true) / 1024);
                list($request, $response, $callback) = $this->queue[$this->request_map[$key]];
                 // Check that the callback is valid, otherwise we work for nothing
                 error_log('Collecting 3 > ' . memory_get_usage(true) / 1024);
                if(is_callable($callback)) {
                    error_log('Collecting 4 > ' . memory_get_usage(true) / 1024);
                    unset($this->request_map[$key]);
                    error_log('Collecting 5 > ' . memory_get_usage(true) / 1024);
                    $response->fromString(static::getLastResponse(curl_multi_getcontent($done['handle'])));
                    error_log('Collecting 6 > ' . memory_get_usage(true) / 1024);                
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
        error_log('Firing 1 > ' . memory_get_usage(true) / 1024);
        list($request, $response) = $this->queue[$i];
        error_log('Firing 2 > ' . memory_get_usage(true) / 1024);
        $curl = static::createCurlHandle();
        error_log('Firing 3 > ' . memory_get_usage(true) / 1024);
        $this->prepare($request, $response, $curl);
        error_log('Firing 4 > ' . memory_get_usage(true) / 1024);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        error_log('Firing 5 > ' . memory_get_usage(true) / 1024);
        curl_multi_add_handle($this->master, $curl);
        error_log('Firing 6 > ' . memory_get_usage(true) / 1024);
        // Add to our request Maps
        $this->request_map[(string) $curl] = $i;
        error_log('Firing 7 > ' . memory_get_usage(true) / 1024);
    }
}
