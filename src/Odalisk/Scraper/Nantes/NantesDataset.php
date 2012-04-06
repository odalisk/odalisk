<?php

namespace Odalisk\Scraper\Nantes;

use Odalisk\Scraper\BaseDataset;

class NantesDataset extends BaseDataset {
    public $url;
    
    public function __construct($buzz, $url) {
        parent::__construct($buzz, $url);
        
        $this->buzz_options[] = 'User-agent: Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.0.1) Gecko/2008071615 Fedora/3.0.1-1.fc9 Firefox/3.0.1';
        
        $this->criteria = array(
            'Category' => '.tx_icsoddatastore_pi1_categories > span.value',
            'Licence' => '.tx_icsoddatastore_pi1_licence > span.value',
            'Update Frequency' => '.tx_icsoddatastore_pi1_updatefrequency > span.value',
            "Date of publication" => '.tx_icsoddatastore_pi1_releasedate > span.value',
            "Last update" => '.tx_icsoddatastore_pi1_updatedate > span.value',
            "Description" => '.tx_icsoddatastore_pi1_description > span.value',
            'Manager' => '.tx_icsoddatastore_pi1_manager > span.value',
            'Owner' => '.tx_icsoddatastore_pi1_owner > span.value',
            "Technical data" => '.tx_icsoddatastore_pi1_technical_data > span.value',
        );
    }
}