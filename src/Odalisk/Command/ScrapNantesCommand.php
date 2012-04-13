<?php

namespace Odalisk\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\FormatterHelper;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\CssSelector\CssSelector;

use Odalisk\Scraper\Tools\RequestDispatcher;
use Odalisk\Scraper\Nantes\NantesPortal;

/**
 * A command that will scrap data from the Nantes' portal
 */
class ScrapNantesCommand extends ScrapCommand {
    protected function configure(){
        $this
            ->setName('odalisk:scrap:nantes')
            ->setDescription('Fetch some data from data.nantes.fr')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $count = 0;
        $start = time();
        
        $this->writeBlock($output, 'Scraping data.nantes.fr');
        
        $portal = new NantesPortal($this->getBuzz());
        $dispatcher = new RequestDispatcher();
        $dispatcher->batchGet($portal->getDatasetsUrls());
        $dispatcher->flush('Odalisk\Scraper\Nantes\NantesPortal::parseDataset');
        foreach($portal->getDatasetsData() as $dataset => $criteria) {
            ++$count;
            $output->writeln('<info>' . $dataset . '</info>');
            foreach($criteria as $criterion => $value) {
                $output->writeln("<info>[$criterion]</info> " . $value);
            }
            
            $this->collectStats($criteria);
        }
        
        $end = time();
        
        $this->writeBlock($output, 'Scraped ' . $count . ' datasets in ' . ($end - $start) . ' seconds.');
        // Display how many of each return code we got
        $this->printStats($output);
    }
}
