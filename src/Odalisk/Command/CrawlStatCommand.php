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
                $data = json_decode(file_get_contents($file->getRealpath()));
                
                if('' == $data->meta->code) {
                    $data->meta->code = 'timeout';
                }
                
                if(array_key_exists($data->meta->code, $stats[$name])) {
                    $stats[$name][$data->meta->code] += 1;
                } else {
                    $stats[$name][$data->meta->code] = 1;
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
