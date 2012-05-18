<?php

namespace Odalisk\Scraper\Tools;

use Buzz\Browser;
use Buzz\Message;

class RequestDispatcher {
    /**
     * Handle to our cURL wrapper library
     *
     * @var Buzz\Browser $buzz
     */
    private $buzz;

    /**
     * Default options passed to buzz,
     *
     * @var array $buzzOptions
     */
    private $buzzOptions;

    /**
     * Our MultiCurlAsync client
     *
     * @var Odalisk\Scraper\Tools\MultiCurlAsync $client
     */
    private $client;

    public function __construct(array $buzzOptions, $timeout = 5) {
        $this->client = new MultiCurlAsync();
        $this->client->setTimeout($timeout);
        $this->buzz = new Browser($this->client);

        $this->buzzOptions = $buzzOptions;
    }

    /**
     * This adds an individual url to the queue, with its callback and headers
     *
     * @param string $url
     * @param string $callback
     * @param array  $options
     */
    public function queueUrl($url, $callback, array $options = array()) {
        // Create the request
        $request = new Message\Request(Message\Request::METHOD_GET, '/', null);

        $request->fromUrl($url);
        $request->addHeaders(array_merge($this->buzzOptions, $options));
        $request->setContent('');

        // Create the response
        $response = new Message\Response();

        // Queue all of it in the cURL pool
        $this->client->queue($request, $response, $callback);
    }

    /**
     * This adds an individual request to the queue, with its callback and headers
     *
     * @param Message\Request $request
     * @param string          $callback
     * @param array           $options
     */
    public function queueRequest($request, $callback, array $options = array()) {

        // Create the response
        $response = new Message\Response();

        // Queue all of it in the cURL pool
        $this->client->queue($request, $response, $callback);
    }

    /**
     * Adds a bunch of urls to the queue, each with a callback and optional parameters
     *
     * @param array  $urls     an array of urls to query
     * @param string $callback the callback function for these urls
     * @param array  $options  optional headers for the request
     */
    public function batchQueue(array $urls, $callback, array $options = array()) {
        foreach ($urls as $url) {
            $this->client->queue($url, $callback, $options);
        }
    }

    /**
     * Call this to start sending off the requests. As they complete, the callbacks will be fired with
     * the response as a parameter.
     *
     * The window size (ie. how many concurrent requests do we fire?)
     * RESPECT the servers, if you overdo it it closely resembles a DDoS attack
     *
     * @param int $windowSize
     */
    public function dispatch($windowSize = 5) {
        $this->client->flush($windowSize);
    }


    public function __call($nom, $args) {

      switch ($nom) {
         case 'queue':
            // If the first arg is a URL, call queueUrl, else queueRequest
            if (is_string($args[0])) {
                $nom = 'queueUrl';
            } elseif ($args[0] instanceof Message\Request) {
                $nom = 'queueRequest';
            }
            if (count($args) < 3) {
                $args[] = array();
            }
            break;
         default:
            die('La fonction RequestDispatcher::'. $nom .'() n\'est pas définie.');

      }


      return call_user_func(array(&$this, $nom), $args[0], $args[1], $args[2]);
   }
}
