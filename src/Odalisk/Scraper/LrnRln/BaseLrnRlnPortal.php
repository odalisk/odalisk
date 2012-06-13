<?php

namespace Odalisk\Scraper\LrnRln;

use Symfony\Component\DomCrawler\Crawler;

use Odalisk\Scraper\BasePortal;
use Odalisk\Scraper\Tools\RequestDispatcher;

abstract class BaseLrnRlnPortal extends BasePortal
{
    // The base url on which the datasets are listed.
    protected $datasetsListUrl;

    // the number of datasets displayed on one page.
    protected $batch_size;

    public function __construct()
    {
        $this->criteria = array(
            'setName' => '//div[@id="centre"]/h2/img',
            'setSummary' => 'div[@id="centre"]/div[@class="left"]/p/p[1]',
            'setReleasedOn' => '//div[@id="centre"]/div[@class="right"]/ul/li/strong[.="Publication"]/..',
            'setLastUpdatedOn' => '//div[@id="centre"]/div[@class="right"]/ul/li/strong[.="Mise à jour"]/..',
            'setCategories' => '//div[@id="centre"]/div[@class="right"]/ul/li/strong[.="Catégorie "]/..',
            'setRawLicense' => '//div[@id="centre"]/div[@class="right"]/ul/li/strong[.="Licence"]/..',
            'setProvider' => '//div[@id="centre"]/div[@class="right"]/ul/li/strong[.="Diffuseur"]/..',
            'setOwner' => '//div[@id="centre"]/div[@class="right"]/ul/li/strong[.="Propriétaire"]/..',
            'setFormats' => "//div[@id='meta']/ul/li/a/strong",
            //'Tags' => "//div[@class="aboutDataset"]/div[4]/dl/dd[3]",
            //'Permissions' => "//div[@class="aboutDataset"]/div[4]/dl/dd[2]",
            //'Time Period' => "//div[@id='centre']/div[4]/ul/li[4]",
            //'Frequency' => // "//div[@class="aboutDataset"]/div[8]/dl/dd[3]/span",
            //'Community Rating' => // "//div[@class="aboutDataset"]/div[3]/dl/dd[1]/div",
        );
    }

    public function getDatasetsUrls()
    {
        $dispatcher = new RequestDispatcher($this->buzzOptions, 30);

        $response = $this->buzz->get($this->datasetsListUrl);
        if (200 == $response->getStatusCode()) {
            // We begin by fetching the number of datasets
            $crawler = new Crawler($response->getContent());
            $node = $crawler->filterXPath('//div[@class="infoD"]');
            if (preg_match("/([0-9]+)/", $node->text(), $match)) {
                $this->estimatedDatasetCount = intval($match[0]);
            }
            error_log('[Get URLs] Estimated number of datasets of the portal : ' . $this->estimatedDatasetCount);

            // Now we fetch the datalists thanks to a <select> ;)
            $nodes = $crawler
                ->filterXPath('//select[@id="datalist"]/option[@value]')
                ->extract(array('value'))
                ;

            // We construct the urls
            $base_url = $this->getBaseUrl();
            $this->urls = array_map(
                    function($id) use($base_url) {
                        return $base_url.substr($id, 3);
                    }
                    , $nodes);
        }

        $this->totalCount = count($this->urls);

        return $this->urls;
    }

    public function additionalExtraction($crawler, &$data) {
        $toSplit = array(
                'setReleasedOn',
                'setLastUpdatedOn',
                'setCategories',
                'setRawLicense',
                'setProvider',
                'setOwner',
                );

        foreach($toSplit as $name) {
            if(isset($data[$name])) {
                $tmp = explode(':', $data[$name]);
                $data[$name] = trim($tmp[1]);
            }
        }

        if(isset($data['setRawLicense'])) {
            $tmp = explode("\n", $data['setRawLicense']);
            $data['setRawLicense'] = $tmp[0];
        }
    }
}
