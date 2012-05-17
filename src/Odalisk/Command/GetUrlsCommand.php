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
class GetUrlsCommand extends BaseCommand {
    protected function configure(){
        $this
            ->setName('odalisk:geturls')
            ->setDescription('Fetch datsets urls for all supported platforms')
            ->addArgument('platform', InputArgument::OPTIONAL, 
                'Which platform ?'
            )
            ->addOption('list', null, InputOption::VALUE_NONE, 
                'If set, the task will display available platforms names'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        // Store the container so that we have an easy shortcut
        $container = $this->getContainer();
        // Get the file dumper
        FileDumper::setBasePath($container->getParameter('file_dumper.data_path'));
        // Get the configuration value from config/app.yml : which platforms are enabled?
        $platform_services = $container->getParameter('app.platforms');
        // Initialize some arrays
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
            
			$path = $container->getParameter('file_dumper.data_path');
            foreach($platforms as $name => $platform) {
				error_log('Get urls from '.$name);
                FileDumper::saveUrls($platform->getDatasetsUrls(), $name);
                error_log($platform->getName() . ' has ' . $platform->getCount() . ' datasets');
            }
        }
    }
}
