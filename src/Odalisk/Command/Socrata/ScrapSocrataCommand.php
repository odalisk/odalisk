<?php

namespace Odalisk\Command\Socrata;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Odalisk\Scraper\Socrata\SocrataPortal;

class ScrapSocrataCommand extends ScrapSocrataTypeCommand {
    protected function configure(){
        $this
            ->setName('odalisk:scrap:socrata')
            ->setDescription('Fetch some data from opendata.socrata.com')
        ;

		$this->callback = 'Odalisk\Scraper\Socrata\SocrataPortal::parseDataset';
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
		$this->portal = new SocrataPortal($this->getBuzz());
		parent::execute($input, $output);
	}

}
