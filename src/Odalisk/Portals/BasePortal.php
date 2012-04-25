<?php

namespace Odalisk\Portals;

use Odalisk\Entity\Portal;

abstract class BasePortal {
    
    protected $buzz;
    protected $buzz_options = array();

	protected $name     = '';
	protected $url      = '';
	protected $base_url = '';

	/**
	 * The api url that retrieves urls of all the datasets of the platform.
	 */
	protected $datasets_api_url;
    
    public function __construct($id = NULL) {
	}

	/**
	 * @return A Portal entity based on these three attributes : $name, $url
	 * and $base_url.
	 */
	public function getNewEntity() {
		$entity = new Portal();
		$entity->setName($this->name);
		$entity->setUrl($this->url);
		$entity->setBaseUrl($this->base_url);

		return($entity);
	}

	public function getName() {
		return($this->name);
	}

}
