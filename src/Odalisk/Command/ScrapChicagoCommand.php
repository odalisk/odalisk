<?php

namespace Odalisk\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\DomCrawler\Crawler;

use Odalisk\Scraper\Tools\RequestDispatcher;
use Odalisk\Scraper\Chicago\ChicagoPortal;

/**
 * A command that will scrap data from the platform of the city of Chicago.
 */
class ScrapChicagoCommand extends ScrapSocrataTypeCommand {

    protected function configure(){
        $this
            ->setName('odalisk:scrap:chicago')
            ->setDescription('Fetch some data from data.cityofchicago.org')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $count = 0;
        $start = time();        
        $this->writeBlock($output, 'Scraping data.cityofchicago.org');
        
        // Create the portal object, that knows how to generate the urls for the datasets.
        $portal = new ChicagoPortal($this->getBuzz());
        
        $chunks = array_chunk($portal->getDatasetsUrls(), 500);
        
        
        foreach($chunks as $urls) {
            // Create a new request dispatcher that will parallelize the process (somewhat)
            $dispatcher = new RequestDispatcher();
            $dispatcher->batchGet($urls);
            $dispatcher->flush('Odalisk\Scraper\Chicago\ChicagoPortal::parseDataset', 20);
        
            // We got everything back, time to process it.
            // Right now, we simply display it on the standard output, but that would be a good place
            // to persist the data in the database
            $datasets = $portal->getDatasetsData();
           
	        
	        foreach($datasets as $dataset => $criteria) {
                if(NULL != $criteria) {
                    ++$count;
                    $output->writeln('<info>' . $dataset . '</info>');
                    foreach($criteria as $criterion => $value) {
                        $output->writeln("<info>[$criterion]</info> " . $value);
                    }
                    
                    $this->collectStats($criteria);
                    
                    // Remove the processed dataset from the index (to free up some memory)
                    $portal->removeDataset($dataset);
                } else {
                    // We don't want to display datasets that haven't been processed yet
                    break;
                }
            }
        }
        
        $end = time();
        
        $this->writeBlock($output, 'Scraped ' . $count . ' datasets in ' . ($end - $start) . ' seconds.');
        // Display how many of each return code we got
        $this->printStats($output);
    }	
}
