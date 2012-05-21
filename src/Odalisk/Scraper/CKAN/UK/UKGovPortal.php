<?php

namespace Odalisk\Scraper\CKAN\UK;

use Odalisk\Scraper\CKAN\BaseCkanPortal;

use Symfony\Component\DomCrawler\Crawler;

/**
 * The scraper for data.gov.uk
 */
class UKGovPortal extends BaseCkanPortal {
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

        if (array_key_exists('setLicense', $data)) {
            if(is_array(json_decode($data['setLicense']))){
                $data['setLicense'] = implode(';', json_decode($data['setLicense']));
            }
                
            if(preg_match('/CCGC\/CCW/i',$data['setLicense'])){
                $data['setLicense'] = "CCW/CROWN";
            }
            if(preg_match('/CCW/i',$data['setLicense'])){
                $data['setLicense'] = "CCW/CROWN";
            }
            if(preg_match('/Crown/i',$data['setLicense'])){
                $data['setLicense'] = "CCW/CROWN";
            }
            
            if(preg_match('/UK Climate Projections Licence/i',$data['setLicense'])){
                $data['setLicense'] = "UK Climate Projections Licence";
            }

            if(preg_match('/^OKD Compliant/i',$data['setLicense'])){
                if(preg_match("/pddl/i", $data['setLicense'])){
                    $data['setLicense'] = "PDDL";
                    return;
                }
                $data['setLicense'] = "ODBL";
            }
            if(preg_match('/digitised at/i',$data['setLicense'])){
                unset($data['setLicense']);
                return;
            }
            if(preg_match('/Other/i',$data['setLicense'])){
                unset($data['setLicense']);
                return;
            }
            if(preg_match('/indicative/i',$data['setLicense'])){
                unset($data['setLicense']);
                return;
            }
            if(preg_match('/unknow/i',$data['setLicense'])){
                unset($data['setLicense']);
                return;
            }
            if(preg_match('/accurate/i',$data['setLicense'])){
                unset($data['setLicense']);
                return;
            }
            if(preg_match('/licence/i',$data['setLicense'])){
                unset($data['setLicense']);
                return;
            }
            if(preg_match('/license/i',$data['setLicense'])){
                unset($data['setLicense']);
                return;
            }
            if(preg_match('/none/i',$data['setLicense'])){
                unset($data['setLicense']);
                return;
            }
            if(empty($data['setLicense'])){
                unset($data['setLicense']);
                return;
            }
            
        }
    }
}
