<?php
namespace Odalisk\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * A command that will download the HTML pages for all the datasets
 */
class FastCrawlCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('odalisk:crawl:fast')
            ->setDescription('Get HTML for all supported platforms');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $start = time();
        
        // Store the container so that we have an easy shortcut
        $container = $this->getContainer();
        // Get the configuration value from config/app.yml : which platforms are enabled?
        $platformServices = $container->getParameter('config.enabled_portals');
        
        $commands = array();
        $proot = $container->getParameter('kernel.project_root');
        $base_command = 'php ' . $proot . '/console odalisk:crawl ';
        
        // Iterate on the enabled platforms to retrieve the actual object
        foreach ($platformServices as $platform) {
            $commands[] = $base_command . $platform;
        }
        
        $process = new \Symfony\Component\Process\Process(implode(" & ", $commands));
        $process->setTimeout(3600);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }
              
        $end = time();
        error_log('[Fast Crawl] Processing ended after ' . ($end - $start) . ' seconds');
    }
}