<?php

namespace Odalisk\Scraper;

use Symfony\Component\DomCrawler\Crawler;

use Buzz\Message;


abstract class BasePlatform {
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
     * The name of the current platform
     *
     * @var string
     */
    protected $name;

    /**
     * The base of a dataset url.
     *
     * @var string
     */
    protected $base_url;

    /**
     * The api url that retrieves urls of all the datasets of the platform.
     *
     * @var string
     */
    protected $api_url;

    protected $criteria;

    protected $count = 0;

    protected $totalCount = 0;

    protected $portal;
    
    /**
     * The date type fields we need to process so we can transform them into DateTime objects
     *
     * @var array $dateFields
     */
    protected $dateFields = array('setReleasedOn', 'setLastUpdatedOn');
    
    /**
     * Match some regex to known date formats. Order is IMPORTANT!
     *
     * @var string
     */
    protected $correctDates = array(
        '/^[0-9]{4}(.[0-9]{1,2}(.[0-9]{1,2}( [0-9]{2}(:[0-9]{2}(:[0-9]{2})?)?)?)?)?$/' =>  array(
            '!Y',
            '!Y*m',
            '!Y*m*d',
            '!Y*m*d H',
            '!Y*m*d H:i',
            '!Y*m*d H:i:s',
        ),
        '/^(([0-9]{1,2}.)?[0-9]{1,2}.)?[0-9]{4}( [0-9]{2}(:[0-9]{2}(:[0-9]{2})?)?)?$/' => array(
            '!Y',
            '!m*Y',
            '!d*m*Y',
            '!d*m*Y H',
            '!d*m*Y H:i',
            '!d*m*Y H:i:s',
        ),
        '/^(([0-9]{1,2}.)?[0-9]{1,2}.)?[0-9]{2}( [0-9]{2}(:[0-9]{2}(:[0-9]{2})?)?)?$/' => array(
            '!y',
            '!m*y',
            '!d*m*y',
            '!d*m*y H',
            '!d*m*y H:i',
            '!d*m*y H:i:s',
        ),
        '/^(?:Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec) ([0-9]{1,2}[^0-9]+)?[0-9]{4}$/' => array(
            '!M*Y',
            '!M*d?*Y',
        ),
        '/^([0-9]{1,2}.)?(?:January|February|March|April|May|June|July|August|September|October|November|December).[0-9]{4}$/' => array(
            '!F?Y',
            '!d?F?Y',
        ),
        '/^([0-9]{1,2}.)?(?:Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec).[0-9]{2}$/' => array(
            '!M?y',
            '!d?M?y',
        ),
        '/^([0-9]{1,2}.)?(?:Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec).[0-9]{4}?$/' => array(
            '!M?Y',
            '!d?M?Y',
        ),
    );
    
    /**
     * Values for date fields that are considered equivalent to empty
     *
     * @var array $emptyDates
     */
    protected $emptyDates = array('N/A', 'n/a', 'TBC', 'not known', '');

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
    abstract public function parsePortal();

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
                    )
                        , 'strlen')
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
        if (array_key_exists('setCategory', $data)) {


            if(is_array(json_decode($data['setCategory']))){
                $data['setCategory'] = implode(';', json_decode($data['setCategory']));
            }

            $data['setCategory'] = trim(implode(';', array_filter(preg_split('/(\s+&\s+|,|;|\s+et\s+|\s+and\s+|\s+\/\s+)/', $data['setCategory']))));

            if(strstr($data['setCategory'], ";and;")){
                echo $data['setCategory']."\n";
            }

            if(0 === preg_match('/[a-zA-Z;]+/', $data['setCategory']) or empty($data['setCategory'])) {
                error_log('[Weird category] ' . $data['setCategory']);
                unset($data['setCategory']);
            }
        }
    }
    
    /**
     * Attemps to transform wild licenses into a set of normalized ones
     *
     * @param array   $data    the data we are gathering
     */
    protected function normalizeLicense(&$data)
    {
        if (array_key_exists('setLicense', $data)) {
            if ($data['setLicense'] == '[]') {
                unset($data['setLicense']);
            } elseif (preg_match('/(OKD Compliant::)?UK Open Government Licence \(OGL\)/', $data['setLicense'])) {
                    $data['setLicense'] = 'OGL';
            }
        }
    }
    
    /**
     * Attemps to transform wild licenses into a set of normalized ones
     *
     * @param array   $data    the data we are gathering
     */
    protected function normalizeFormat(&$data)
    {

        $wild_formats = array("/.*kmz.*/i","/.*csv.*/i","/.*xml.*/i","/.*pdf.*/i","/((.*(xls|vnd.ms-excel).*)|excel)/i","/.*(html|htm).*/i","/text.*/i","/rdf/i","/ppt/i","/.*shp.*/i","/.*(vnd.ms-word|doc).*/i","/.*zip.*/i","/.*json.*/i","/rss/i","/api/i","/wms/i","/.*(Otros|Unverified).*/i","/asp/i","/(image\/jpg|jpg)/i","/atom/i","/.*(openDOCument.spreadsheet|ods).*/i","/gtfs/i");
        $normalized_formats = array("KMZ","CSV","XML","PDF","XLS","HTML","TXT","RDF","PPT","SHP","DOC","ZIP","JSON","RSS","API","WMS","Unknown","ASP","JPG","ATOM","ODS","GTFS");

        if (array_key_exists('setFormat', $data)) {
                

                $formats = preg_split('/;/',$data['setFormat']);
                $formats = array_unique($formats);
                $output = array();
                foreach ($formats as $format) {

                    $format = preg_replace('/\s+/','', $format);
                    $format = preg_replace($wild_formats, $normalized_formats, $format,1);

                    if(!in_array($format,$normalized_formats)){
                       
                       error_log('[Weird Format] ' . $format." : ".$data['setName']);
                    }

                    $output[] = $format;
                 }
                
                $data['setFormat'] = implode(';', array_unique($output));
        }
    }

    /**
     * Do some magic on the dates to transform them into datetime objects
     *
     * @param array   $data    the data we are gathering
     */
    protected function parseDates(&$data)
    {
        // We transform dates strings in datetime.
        foreach ($this->dateFields as $field) {
            if (array_key_exists($field, $data)) {
                $date = $data[$field];
                // Try to match the date against something we know
                foreach($this->correctDates as $regex => $formats) {
                    // Check if we have a match
                    if(preg_match($regex, $date, $m)) {
                        // Depending on how many matches we have, we know which format to pick
                        $data[$field] = \Datetime::createFromFormat($formats[count($m)-1], $date)->format("d m Y");
                        if(false === $data[$field]) {
                            error_log('[>>> False positive ] ' . $date . ' with ' . $regex . ' (count = ' . (count($m)-1) .')');
                            $data[$field] = null;
                        }
                        // Check out the next field directly
                        continue 2;
                    }
                }
                // This is executed only if we have no match
                // Check if it is a known empty-ish value
                if(in_array($date, $this->emptyDates)) {
                    $data[$field] = null;
                } else {
                    // Not something we recognize
                    error_log('[Unknown date format ] ' . $date);
                    $data[$field] = $date;
                }
                $date = null;
            }
        }
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
    
    public function setBuzz(\Buzz\Browser $buzz, $timeout = 30) {
        $this->buzz = $buzz;
        $this->buzz->getClient()->setTimeout($timeout);
    }

    public function setBuzzOptions(array $options) {
        $this->buzzOptions = $options;
    }

    public function setDoctrine($doctrine) {
        $this->doctrine = $doctrine;
        $this->em = $this->doctrine->getEntityManager();
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }

    public function setBaseUrl($base_url) {
        $this->base_url = $base_url;
    }

    public function getBaseUrl() {
        return $this->base_url;
    }

    public function setApiUrl($api_url) {
        $this->api_url = $api_url;
    }

    public function getCount() {
        return $this->totalCount;
    }
}
