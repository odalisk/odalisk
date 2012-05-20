<?php

namespace Odalisk\Scraper\CKAN\UK;

use Odalisk\Scraper\CKAN\BaseCKAN;

use Symfony\Component\DomCrawler\Crawler;

/**
 * The scraper for data.gov.uk
 */
class UKPlatform extends BaseCKAN {
    public function __construct() {
        $this->criteria = array(
            'setName' => '//h1[@class="title"]',
            'setSummary' => '//div[@class="package_title"]',
            'setReleasedOn' => '//td[.="Released" and @class="package_label"]/../td[2]/div[1]',
            'setLastUpdatedOn' => '//td[.="Last updated" and @class="package_label"]/../td[2]/div[1]',
            'setProvider' => '//td[.="Published by" and @class="package_label"]/../td[2]/div[1]',
            'setLicense' => '//td[.="Licence" and @class="package_label"]/../td[2]/div[1]',
            'setCategories' => './/*[@class="package_label" and text() = "Categories"]/following-sibling::*'
        );

        $this->dateFormat = 'Y-m-d';
    }
    
    protected function additionalExtraction($crawler, &$data)
    {
        if (!array_key_exists('setReleasedOn', $data)) {
            $nodes = $crawler->filterXPath('//*[(@id = "tagline")]');

            if (0 < count($nodes)) {
                $content = trim(join(
                    ";",
                    $nodes->each(
                        function($node,$i) {
                            return $node->nodeValue;
                        }
                    )
                ));

                if (preg_match('/^Posted by ([a-zA-Z &,\'-]+) on ([0-9]{2}\/[0-9]{2}\/[0-9]{4})/', $content, $matches)){
                    $data['setProvider'] = $matches[1];
                    $data['setReleasedOn'] = $matches[2];
                } else {
                    error_log('>>' . $content);
                }
            }
        }
    }

    public function parsePortal() {
        $this->portal = new \Odalisk\Entity\Portal();
        $this->portal->setName($this->getName());
        $this->portal->setUrl('http://data.gov.uk/');
        $this->portal->setCountry($this->country);
        $this->portal->setStatus($this->status);
        $this->portal->setEntity($this->entity);
        $this->em->persist($this->portal);
        $this->em->flush();
    }
}
