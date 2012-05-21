<?php

namespace Odalisk\Scraper;

use Symfony\Component\DomCrawler\Crawler;

use Buzz\Message;

use Odalisk\Scraper\Tools\Normalize\CategoryNormalizer;


abstract class BasePortal {
    /**
     * Buzz instance
     *
     * @var Buzz\Browser
     */
    protected $buzz;

    /**
     * Default options for Buzz
     *
     * @var array
     */
    protected $buzzOptions = array();

    /**
     * The doctrine handle
     *
     * @var string
     */
    protected $doctrine;

    /**
     * Entity manager
     *
     * @var string
     */
    protected $em;
    
    /**
     * The configuration values of this portal
     *
     * @var string
     */
    protected $config;

    protected $criteria;

    protected $count = 0;

    protected $totalCount = 0;

    protected $portal;
    
    
    protected $categoryNormalizer;
    protected $formatNormalizer;
    protected $dateNormalizer;

    /**
     * The platform's datasets URLs list
     * 
     * @var array $urls
     */
    protected $urls = array();

    protected $urlsListIndexPath;

    abstract public function getDatasetsUrls();

    public function crawlDatasetsList(Message\Request $request, Message\Response $response) {

        if ($response->getStatusCode() != 200) {
            error_log('[Get URLs] Failed to download ' . $request->getUrl() . '. Skipping.');

            return;
        }

        $crawler = new Crawler($response->getContent());
        $nodes = $crawler->filterXPath($this->urlsListIndexPath);
        if (0 < count($nodes)) {
            $this->urls = array_merge($this->urls, $nodes->extract(array('href')));
        }

        $count = count($this->urls);
        if (0 == $count % 100) {
            error_log('[Get URLs] ' . $count . ' / ' . $this->estimatedDatasetCount . ' done (estimated)');
        }
    }

    public function prepareRequestsFromUrls($urls) {
        return $urls;
    }
    
    /**
     * Load the portal object from the database. If none is found, parse it from the website.
     *
     * @return Portal
     */
    public function loadPortal() {
        $this->portal = $this->em->getRepository('Odalisk\Entity\Portal')
            ->findOneByName($this->getName());

        if (null == $this->portal) {
            $this->parsePortal();
        }

        return $this->portal;
    }

    public function getPortal() {
        return $this->portal;
    }

    /**
     * Fetch the portal from the web, parse it and create a new entity in $this->portal (and persist/flush it)
     *
     * @return void
     */
    public function parsePortal() {
        $this->portal = new \Odalisk\Entity\Portal();
        
        $this->portal->setName($this->getName());
        $this->portal->setUrl($this->getBaseUrl());
        $this->portal->setCountry($this->getCountry());
        $this->portal->setStatus($this->getStatus());
        $this->portal->setEntity($this->getEntity());
        
        $this->em->persist($this->portal);
        $this->em->flush();
    }

    public function analyseHtmlContent($html, &$dataset) 
    {
        $crawler = new Crawler($html);
        $data = array();

        if (0 != count($crawler)) {
            // Default data extraction process
            $this->defaultExtraction($crawler, $data);
            
            // If the default implementation is not smart enough, you can add your own logic here
            $this->additionalExtraction($crawler, $data);
            
            // This is the default, it should be good enough for most cases
            $this->defaultNormalization($data);
            
            // If the default implementation is not smart enough, you can add your own logic here
            $this->additionalNormalization($data);
        }

        $dataset->populate($data);
        $crawler = null;
        $data = null;
    }
    
    /**
     * This function goes through the criteria array, and extracts the information. If
     * an XPath expression yields several nodes, their value is joined using the ';'
     * character.
     *
     * @param Crawler $crawler the crawler
     * @param array   $data    the data we are gathering
     */
    protected function defaultExtraction($crawler, &$data) 
    {
        foreach ($this->criteria as $name => $path) {
            $nodes = $crawler->filterXPath($path);
            if (0 < count($nodes)) {
                $data[$name] = join(
                    ";",
                    array_filter(
                        $nodes->each(
                            function($node,$i) {
                                return trim($node->nodeValue);
                            }
                        ), 
                        'strlen'
                    )
                );
            }
        }
    }
    
    /**
     * Override this function in subclasses if you find that the default one is not
     * good enough. This implementation does nothing.
     *
     * @param Crawler $crawler the crawler
     * @param array   $data    the data we are gathering
     */
    protected function additionalExtraction($crawler, &$data) 
    {
    }
    
    protected function defaultNormalization(&$data)
    {
        $this->normalizeSummary($data);
        $this->normalizeCategory($data);
        $this->normalizeLicense($data);
        $this->normalizeFormat($data);
        $this->parseDates($data);
    }
    
    /**
     * Trim the summary if it exists
     *
     * @param array   $data    the data we are gathering
     */
    protected function normalizeSummary(&$data)
    {
        if (array_key_exists('setSummary', $data)) {
            $data['setSummary'] = trim($data['setSummary']);
        }
    }
    
    /**
     * Trim categories and transform it into a lower-case, semi-colon separated list
     *
     * @param array   $data    the data we are gathering
     **/
    protected function normalizeCategory(&$data)
    {
        if (array_key_exists('setCategories', $data)) {
            $categories = $this->categoryNormalizer->getCategories($data['setCategories']);
            $data['setRawCategories'] = $categories['raw'];
            unset($categories['raw']);
            $data['setCategories'] = $categories;
        }
    }
    
    /**
     * Attemps to transform wild licenses into a set of normalized ones
     *
     * @param array   $data    the data we are gathering
     */
    protected function normalizeLicense(&$data)
    {
        
        if (array_key_exists('setRawLicense', $data)) {
            if ($data['setRawLicense'] == '[]') {
                unset($data['setRawLicense']);
            } elseif (preg_match('/(OKD Compliant::)?UK Open Government Licence \(OGL\)/', $data['setLicense'])) {
                $data['setRawLicense'] = 'OGL';
            }
        }
    }
    
    /**
     * Attemps to transform wild formats into a set of normalized ones
     *
     * @param array   $data    the data we are gathering
     */
    protected function normalizeFormat(&$data)
    {
        if (array_key_exists('setFormats', $data)) {
            $formats = $this->formatNormalizer->getFormats($data['setFormats']);
            $data['setRawFormats'] = $formats['raw'];
            unset($formats['raw']);
            $data['setFormats'] = $formats;
        }
    }

    /**
     * Do some magic on the dates to transform them into datetime objects
     *
     * @param array   $data    the data we are gathering
     */
    protected function parseDates(&$data)
    {
        $this->dateNormalizer->normalize($data);
    }
    
    /**
     * Override this function in subclasses if you find that the default one is not
     * good enough. This implementation does nothing.
     *
     * @param array   $data    the data we are gathering
     */
    protected function additionalNormalization(&$data)
    {
    }
    
    public function setBuzz(\Buzz\Browser $buzz, $timeout = 30, $options) {
        $this->buzz = $buzz;
        $this->buzz->getClient()->setTimeout($timeout);
        $this->buzzOptions = $options;
    }

    public function setDoctrine($doctrine) {
        $this->doctrine = $doctrine;
        $this->em = $this->doctrine->getEntityManager();
    }
    
    public function setCategoryNormalizer($normalizer)
    {
        $this->categoryNormalizer = $normalizer;
    }
    
    public function setFormatNormalizer($normalizer)
    {
        $this->formatNormalizer = $normalizer;
    }
    
    public function setDateNormalizer($normalizer)
    {
        $this->dateNormalizer = $normalizer;
    }
    
    public function setConfiguration($config)
    {
        $this->config = $config;
    }

    public function getTotalCount()
    {
        return $this->totalCount;
    }
    
    public function __call($name, $arguments) {
        if(0 === strpos($name, 'get')) {
            if(array_key_exists($name, $this->config)) {
                return $this->config[$name];
            } else {
                $property = substr($name, 3);
                $property[0] = strtolower($property[0]);
                $property = preg_replace_callback(
                    '/[A-Z]/',
                    function($match) {
                        return '_' . strtolower($match[0]);
                    },
                    $property
                );
                
                if(array_key_exists($property, $this->config)) {
                    $this->config[$name] = $this->config[$property];
                    return $this->config[$name];
                }
            }
        }
    }
}
