<?php

namespace Odalisk\Portals\Socrata;

use Odalisk\Portals\BasePortal;

abstract class AbstractSocrata extends BasePortal {

    protected static $criteria = array(
        'Creation' => '//span[@class="aboutCreateDate"]/span',
        'Description' => '//div[@class="aboutDataset"]/div[2]/div/p',
        'Last update' => '//span[@class="aboutUpdateDate"]/span',
        'Category' => '//div[@class="aboutDataset"]/div[4]/dl/dd[1]',
        'Tags' => '//div[@class="aboutDataset"]/div[4]/dl/dd[3]',
        'Permissions' => '//div[@class="aboutDataset"]/div[4]/dl/dd[2]',
        'Data Provider' => '//div[@class="aboutDataset"]/div[7]/dl/dd[1]',
        'Data Owner' => '//div[@class="aboutDataset"]/div[8]/dl/dd[1]/span',
        'Time Period' => '//div[@class="aboutDataset"]/div[8]/dl/dd[2]/span',
        'Frequency' => '//div[@class="aboutDataset"]/div[8]/dl/dd[3]/span',
        'Community Rating' => '//div[@class="aboutDataset"]/div[3]/dl/dd[1]/div',  
    );
    
    public function __construct($id = NULL) {
        parent::__construct($id);
    }
}
