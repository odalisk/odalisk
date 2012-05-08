<?php

namespace Odalisk\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\DomCrawler\Crawler;

/**
 * A command that will scrap data from the Nantes' portal
 */
class ScrapAllCommand extends ScrapCommand {
    protected function configure(){
        $this
            ->setName('odalisk:scrap:all')
            ->setDescription('Fetch data for all supported platforms')
            ->addArgument('platform', InputArgument::OPTIONAL, 
                'Which platform do you want to scrap?'
            )
            ->addOption('list', null, InputOption::VALUE_NONE, 
                'If set, the task will display available platforms names rather than scrap them'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        //$count = 0;
        //$start = time();
        
        $container = $this->getContainer();
        $dispatcher = $container->get('request_dispatcher');
        $platform_services = $container->getParameter('app.platforms');
        $platforms = array();
        $queries = array();
        
        
        if ($input->getOption('list')) {
            foreach($platform_services as $platform) {
                $output->writeln('<info>' . $platform . '</info>');
            }
        } else {
            if($platform = $input->getArgument('platform')) {
                 $platform_services = array($platform);
            }
            
            foreach($platform_services as $platform) {
                // Get the platform object
                $platforms[$platform] = $container->get($platform);
            }
            
            foreach($platforms as $name => $platform) {
                foreach($platform->getDatasetsUrls() as $url) {
                    $queries[] = array('url' => $url, 'platform' => $name);
                }
            }
            
            // This way we dispatch concurrent queries on several servers (somewhat)
            shuffle($queries);

            foreach($queries as $query) {
                $dispatcher->queue($query['url'], array($platforms[$query['platform']], 'parseDataset'));
            }

            $dispatcher->dispatch(10);
            $container->get('doctrine')->getEntityManager()->flush();
        }
        
        /*        
        $this->writeBlock($output, 'Scraping data.gov.uk');
        
        // Create the portal object, that knows how to generate the urls for the datasets.
        $portal = new UkPortal($this->getBuzz());
        
        // We got about 8000 datasets, so we process them in batches of 500 to keep the memory footprint within
        // acceptable limits (256 Mo)
        
        
        foreach($chunks as $urls) {
            // Create a new request dispatcher that will parallelize the process (somewhat)
            $dispatcher = new RequestDispatcher();
            $dispatcher->batchGet($urls);
            $dispatcher->flush('Odalisk\Scraper\UK\UkPortal::parseDataset', 20);
        
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
        */
    }
}
