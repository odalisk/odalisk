<?php

namespace Odalisk\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Finder\Finder;

/**
 * A command that will download the HTML pages for all the datasets
 */
class CrawlStatCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('odalisk:crawlstat')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Store the container so that we have an easy shortcut
        $container = $this->getContainer();
        $platformServices = $container->getParameter('config.enabled_portals');
        $path = $container->getParameter('kernel.project_root') . '/data/';
        $cmd = 'cut -c 17-19 ';
        // Initialize some arrays
        $stats = array();
        
        foreach($platformServices as $name) {
            $stats[$name] = array();
            $stats[$name]['total'] = 0;
            $finder = new Finder();
            $finder->in($path . $name)
                ->notName("*.json")
                ->files();
            
            foreach($finder as $file) {
                $data = exec($cmd . $file->getRealpath());
                
                if('' == $data) {
                    $data = 'timeout';
                }
                
                if(array_key_exists($data, $stats[$name])) {
                    $stats[$name][$data] += 1;
                } else {
                    $stats[$name][$data] = 1;
                }
                $stats[$name]['total'] += 1;
            }
            
            error_log('Stats for : ' . $name);
            foreach($stats[$name] as $code => $count) {
                error_log($code . ' => ' . $count . '/' . $stats[$name]['total']);
            }
        }
    }
}

