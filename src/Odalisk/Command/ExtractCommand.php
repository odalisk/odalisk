<?php

namespace Odalisk\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * A command that will download the HTML pages for all the datasets
 */
class ExtractCommand extends BaseCommand {
    protected function configure(){
        $this
            ->setName('odalisk:extract')
            ->setDescription('Analyse HTML for all supported platforms')
            ->addArgument('platform', InputArgument::OPTIONAL, 
                'Which platform do you want to analyse?'
            )
            ->addOption('list', null, InputOption::VALUE_NONE, 
                'If set, the task will display available platforms names rather than analyse them'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $start = time();
        // Store the container so that we have an easy shortcut
        $container = $this->getContainer();
        // Get the configuration value from config/app.yml : which platforms are enabled?
        $platform_services = $container->getParameter('app.platforms');
        // Get the data directory
        $data_path = $container->getParameter('file_dumper.data_path');
        // Entity repository for datasets_crawls & entity manager
        $er = $this->getEntityRepository('Odalisk\Entity\DatasetCrawl');
        $em = $this->getEntityManager();
        $em->getConnection()->getConfiguration()->setSQLLogger(null);
        
        // Initialize some arrrays
        $platforms = array();
        
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
            //  - get successful crawls from the databse
            //  - parse the corresponding files
            foreach($platforms as $name => $platform) {
                error_log('Analyzing ' . $platform->getName());
                // Load the portal object from the database
                $portal = $platform->loadPortal();
                // Get successful crawls
                $crawls = $er->getSuccessfullCrawls($portal);
                $total = count($crawls);
                // Cache the platform path
                $platform_path = $data_path . $name . '/';
                
                $count = 0;
                foreach($crawls as $crawl) {
                    $count++;
                    $dataset = new \Odalisk\Entity\Dataset();
                    $dataset->setUrl($crawl[0]->getUrl());
                    $dataset->setCrawl($crawl[0]);
                    $dataset->setPortal($portal);
                    
                    $html = file_get_contents($platform_path . $crawl[0]->getHash());
                    
                    $platform->parseFile($html, $dataset);
                    $em->persist($dataset);
                    
                    if(0 == $count % 100) {
                       error_log('> ' . $count . ' / ' . $total . ' done');
                       error_log('> ' . memory_get_usage(true) / (1024 * 1024));
                    }
                }
                error_log('Flushing data !');
                $em->flush();
                unset($crawls);
            }
        }
        $end = time();
        error_log('Processing ended after ' . ($end - $start) . ' seconds');
    }
}
