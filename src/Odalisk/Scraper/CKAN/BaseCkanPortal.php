<?php

namespace Odalisk\Scraper\CKAN;


use Odalisk\Scraper\BasePortal;


abstract class BaseCkanPortal extends BasePortal
{
    protected $datasets = array();

    public function __construct()
    {
        $this->criteria = array(
            'setName' => '//h2[@id="datasetName" and @class="clipText currentViewName"]',
            'setSummary' => '//p[@class=""]',
            'setReleasedOn' => '//span[@class="aboutCreateDate"]/span',
            'setSummary' => '//div[@class="aboutDataset"]/div[2]/div/p',
            'setLastUpdatedOn' => '//span[@class="aboutUpdateDate"]/span',
            'setCategories' => '//div[@class="aboutDataset"]/div[4]/dl/dd[1]',
            //, 'Tags' => '//div[@class="aboutDataset"]/div[4]/dl/dd[3]'
            //, 'Permissions' => '//div[@class="aboutDataset"]/div[4]/dl/dd[2]',
            'setProvider' => '//div[@class="aboutDataset"]/div[7]/dl/dd[1]',
            'setOwner' => '//div[@class="aboutDataset"]/div[8]/dl/dd[1]/span',
            // , 'Time Period' => '//div[@class="aboutDataset"]/div[8]/dl/dd[2]/span'
            // , 'Frequency' => '//div[@class="aboutDataset"]/div[8]/dl/dd[3]/span'
            // , 'Community Rating' => '//div[@class="aboutDataset"]/div[3]/dl/dd[1]/div'
        );
    }

    public function getDatasetsUrls()
    {
        $urls = array();

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
}
