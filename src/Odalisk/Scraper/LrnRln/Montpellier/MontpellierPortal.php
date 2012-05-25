
<?php

namespace Odalisk\Scraper\LrnRln\Montpellier;

use Odalisk\Scraper\LrnRln\BaseLrnRlnPortal;

class MontpellierPortal extends BaseLrnRlnPortal
{
    public function __construct()
    {
        parent::__construct();
        $this->datasetsListUrl = 'http://opendata.montpelliernumerique.fr/Les-donnees/';
    }
}
