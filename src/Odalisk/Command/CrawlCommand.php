<?php

namespace Odalisk\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

use Odalisk\Scraper\Tools\FileDumper;

/**
 * A command that will download the HTML pages for all the datasets
 */
class CrawlCommand extends BaseCommand {
    protected function configure(){
        $this
            ->setName('odalisk:crawl')
            ->setDescription('Fetch HTML for all supported platforms')
            ->addArgument('platform', InputArgument::OPTIONAL, 
                'Which platform do you want to crawl?'
            )
            ->addOption('list', null, InputOption::VALUE_NONE, 
                'If set, the task will display available platforms names rather than crawl them'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $start = time();
        // Store the container so that we have an easy shortcut
        $container = $this->getContainer();
        // Get the request dispatcher
        $dispatcher = $container->get('request_dispatcher');
        // Get the file dumper
        $this->initDumper($container->getParameter('file_dumper.data_path'), $container->get('doctrine'));
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
                // Store the platform object
                $platforms[$platform] = $container->get($platform);
            }
            
            // Process each platform :
            //  - get the urls for the datasets and add them to the queue
            //  - Save the data on disk
            foreach($platforms as $name => $platform) {
                // Create one URL list / platform
                $queries[$name] = array();
                // Load the portal object from the database
                $platform->loadPortal();
                // Add a base URL => name,portal mapping to the file dumper
                FileDumper::addMapping($name, $platform->getBaseUrl(), $platform->getPortal());
                // Get the URLs
                $queries[$name] = $platform->prepareRequestsFromUrls(FileDumper::getUrls($name));
                // Log how many URLs we added
                FileDumper::setTotalCount(FileDumper::getTotalCount() + count($queries[$name]));
                //error_log($platform->getName() . ' has ' . $platform->getCount() . ' datasets');
            }
            
            // While our url pool isnt empty
            while(count($queries) > 0) {
                // Pick an url from each queue and add it
                foreach($queries as $name => &$queue) {
                    // Get the last element of this queue
                    $url = array_pop($queue);
                    // Add it to the dispatcher it isn't NULL
                    if(NULL != $url) {
                        $dispatcher->queue($url, 'Odalisk\Scraper\Tools\FileDumper::saveToDisk');
                    } else {
                        // We reached the end of the queue, remove it from the pool
                        unset($queries[$name]);
                    }
                }
            }
            
            // Launch the crawl
            error_log('[Get HTML] Starting to crawl');
            $dispatcher->dispatch(10);
            
            $end = time();
            error_log('[Get HTML] Processing ended after ' . ($end - $start) . ' seconds');
        }
    }
    
    public function initDumper($path, $doctrine) {
        FileDumper::setDoctrine($doctrine);
        FileDumper::setBasePath($path);
    }
}
