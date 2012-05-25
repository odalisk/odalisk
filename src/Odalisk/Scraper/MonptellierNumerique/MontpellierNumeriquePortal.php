
<?php

namespace Odalisk\Scraper\Socrata;

use Symfony\Component\DomCrawler\Crawler;

use Odalisk\Scraper\BasePortal;
use Odalisk\Scraper\Tools\RequestDispatcher;

abstract class MontpellierNumeriquePortal extends BasePortal
{
    // The base url on which the datasets are listed.
    protected $datasetsListUrl;

    // the number of datasets displayed on one page.
    protected $batch_size;

    public function __construct()
    {
        $this->criteria = array(
            'setName' => '//*div[@id='centre']/div[4]/ul/li'
            , 'setSummary' => '//*div[@id='centre']/div[3]/p[3]'
            , 'setReleasedOn' => '//*div[@id='centre']/div[4]/ul/li[3]'
            , 'setLastUpdatedOn' => '//*div[@id='centre']/div[4]/ul/li[2]'
            , 'setCategories' => '//*div[@id='centre']/div[4]/ul/li[7]'
            , 'setRawLicense' => '//*div[@id='centre']/div[4]/ul/li[10]'
            //, 'Tags' => '//div[@class="aboutDataset"]/div[4]/dl/dd[3]'
            //, 'Permissions' => '//div[@class="aboutDataset"]/div[4]/dl/dd[2]'
            , 'setProvider' => '//*div[@id='centre']/div[4]/ul/li[6]'
            , 'setOwner' => '//*div[@id='centre']/div[4]/ul/li[5]'
            , 'Time Period' => '//*div[@id='centre']/div[4]/ul/li[4]'
            // , 'Frequency' => '//div[@class="aboutDataset"]/div[8]/dl/dd[3]/span'
            // , 'Community Rating' => '//div[@class="aboutDataset"]/div[3]/dl/dd[1]/div'
        );

        $this->urlsListIndexPath = '//td[@class="nameDesc"]/a';
        $this->batch_size = 10;
    }

    public function getDatasetsUrls()
    {
        $urls = array();

		//Downloads the csv of the 

        // Make the API call
        $response = $this->buzz->get(
            $this->getApiUrl(),
            $this->buzzOptions
        );

        // Get the paths
        if (200 == $response->getStatusCode()) {
            $data = json_decode($response->getContent());

            foreach ($data as $key => $dataset_name) {
                $urls[] = $this->getBaseUrl() . $dataset_name;
            }
        } else {
            error_log('Couldn\'t fetch list of datasets for ' . $this->getName());
        }

        $this->totalCount = count($urls);

        return $urls;
    }

    protected function additionalExtraction($crawler, &$data)
    {
        $data['setFormats'] = "CSV;JSON;PDF;RDF;RSS;XLS;XLSX;XML";
    }
}

