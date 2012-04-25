<?php

namespace Odalisk\Portals\Socrata;

use Odalisk\Portals\Socrata\AbstractSocrata;

class NY extends AbstractSocrata {

    public function __construct($id = NULL) {
        parent::__construct($id);
		$this->name     = 'ny';
		$this->url      = 'https://nycopendata.socrata.com/';
		$this->base_url = 'https://nycopendata.socrata.com/d/';
    }
}
