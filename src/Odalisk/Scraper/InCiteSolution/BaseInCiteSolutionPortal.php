<?php

namespace Odalisk\Scraper\InCiteSolution;

use Buzz\Message;

use Odalisk\Scraper\BasePortal;


/**
 * The scraper for in cite Solution Plateform
 */
abstract class BaseInCiteSolutionPortal extends BasePortal
{

    public function __construct()
    {
        $this->criteria = array(
            'setName' => ".//*[@class='tx_icsoddatastore_pi1_single']/h1",
            'setCategories' => ".//*[@class='tx_icsoddatastore_pi1_categories separator']/span[@class='value']",
            'setRawLicense' => ".//*[@class='tx_icsoddatastore_pi1_licence separator']/span[@class='value']",
            // 'Update Frequency' => ".//*[@class='tx_icsoddatastore_pi1_updatefrequency separator']/span[@class='value']",
            'setReleasedOn' => ".//*[@class='tx_icsoddatastore_pi1_releasedate separator']/span[@class='value']",
            'setLastUpdatedOn' => ".//*[@class='tx_icsoddatastore_pi1_updatedate separator']/span[@class='value']",
            'setSummary' => ".//*[@class='tx_icsoddatastore_pi1_description separator']/span[@class='value']",
            'setMaintainer' => ".//*[@class='tx_icsoddatastore_pi1_manager separator']/span[@class='value']",
            'setOwner' => ".//*[@class='tx_icsoddatastore_pi1_owner separator']/span[@class='value']",
            //'Technical data' => ".//*[@class='tx_icsoddatastore_pi1_technical_data separator']/span[@class='value']",
            'setFormats' => ".//*[@class='tx_icsoddatastore_pi1_file']/a/img/@alt",
            // 'setProvider' => " "
        );
    }

    public function getDatasetsUrls()
    {
        // API Call
        $urls = array();

        $response = $this->buzz->get(
            $this->getApiUrl(),
            $this->buzzOptions
        );

        if (200 == $response->getStatusCode()) {
            $data = json_decode($response->getContent());
            foreach ($data->opendata->answer->data->dataset as $dataset) {
                $urls[] = $this->getBaseUrl() . 'donnees/detail/?tx_icsoddatastore_pi1[uid]=' . $dataset->id;
            }
        } else {
            error_log('Couldn\'t fetch list of datasets for ' . $this->name);
        }

        $this->totalCount = count($urls);

        return $urls;
    }

    public function prepareRequestsFromUrls($urls)
    {
        $factory = new Message\Factory();
        $requests = array();

        foreach ($urls as $url) {
            $formRequest = $factory->createFormRequest();
            $formRequest->setMethod(Message\Request::METHOD_POST);
            $formRequest->fromUrl($this->sanitize($url));
            $formRequest->addHeaders($this->buzzOptions);
            $formRequest->setFields(array('tx_icsoddatastore_pi1[cgu]' => 'on'));
            $requests[] = $formRequest;
        }

        return $requests;
    }

    public function sanitize($url)
    {
        return str_replace(']', '%5D', str_replace('[', '%5B', $url));
    }
}
