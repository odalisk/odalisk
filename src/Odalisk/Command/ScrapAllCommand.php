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
class ScrapAllCommand extends BaseCommand {
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
        // Store the container so that we have an easy shortcut
        $container = $this->getContainer();
        // Get the request dispatcher
        $dispatcher = $container->get('request_dispatcher');
        // Get the configuration value from config/app.yml : which platforms are enabled?
        $platform_services = $container->getParameter('app.platforms');
        // Initialize some arrrays
        $platforms = array();
        $queries = array();
        
        // If the --list switch was used, just list the enabled platforms names
        if ($input->getOption('list')) {
            foreach($platform_services as $platform) {
                $output->writeln('<info>' . $platform . '</info>');
            }
        } else {
            // If we get an argument, replace the platform_services array with one containing just that plaform
            if($platform = $input->getArgument('platform')) {
                 $platform_services = array($platform);
            }
            
            // Iterate on the enabled platforms to retrieve the actual object
            foreach($platform_services as $platform) {
                // Get the platform object
                $platforms[$platform] = $container->get($platform);
            }
            
            // Process each platform :
            //  - get the datasets already stored from the database
            //  - get the urls for the datasets and add them to the queue
            foreach($platforms as $name => $platform) {
                $queries[$name] = array();
                $platform->loadPortal();
                foreach($platform->getDatasetsUrls() as $url) {
                    $queries[$name][] = array('url' => $url, 'platform' => $name);
                }
                error_log($platform->getName() . ' has ' . $platform->getCount() . ' datasets');
            }
            
            // While our url pool isnt empty
            while(count($queries) > 0) {
                // Pick an url from each queue and add it
                foreach($queries as $name => &$queue) {
                    // Get the last element of this queue
                    $query = array_pop($queue);
                    // Add it to the dispatcher it isn't NULL
                    if(NULL != $query) {
                        $dispatcher->queue($query['url'], array($platforms[$query['platform']], 'parseDataset'));
                    } else {
                        // We reached the end of the queue, remove it from the pool
                        unset($queries[$name]);
                    }
                }
            }
            
            $dispatcher->dispatch(30);
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
