<?php

namespace Odalisk\Portals\Socrata;

use Odalisk\Portals\Socrata\AbstractSocrata;

class Socrata extends AbstractSocrata {

    public function __construct($id = NULL) {
        parent::__construct($id);
		$this->name     = 'socrata';
		$this->url      = 'https://opendata.socrata.com/';
		$this->base_url = 'https://opendata.socrata.com/d/';
    }
}
