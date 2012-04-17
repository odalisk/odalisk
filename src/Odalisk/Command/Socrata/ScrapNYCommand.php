<?php

namespace Odalisk\Command\Socrata;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Odalisk\Scraper\Socrata\NYPortal;

class ScrapNYCommand extends ScrapSocrataTypeCommand {
    protected function configure(){
        $this
            ->setName('odalisk:scrap:ny')
            ->setDescription('Fetch some data from nycopendata.socrata.com')
        ;

		$this->callback = 'Odalisk\Scraper\Socrata\NYPortal::parseDataset';
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
		$this->portal = new NYPortal($this->getBuzz());
		parent::execute($input, $output);
	}

}
