<?php

namespace Odalisk\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * A command that will download the HTML pages for all the datasets
 */
class ExtractCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('odalisk:extract')
            ->setDescription('Analyse HTML for all supported platforms')
            ->addArgument('platform', InputArgument::OPTIONAL,
                'Which platform do you want to analyse?'
            )
            ->addOption('list', null, InputOption::VALUE_NONE,
                'If set, the task will display available platforms names rather than analyse them'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $start = time();
        // Store the container so that we have an easy shortcut
        $container = $this->getContainer();
        // Get the configuration value from config/app.yml : which platforms are enabled?
        $platformServices = $container->getParameter('app.platforms');
        // Get the data directory
        $dataPath = $container->getParameter('file_dumper.data_path');
        // Entity repository for datasets_crawls & entity manager
        $em = $this->getEntityManager();
        $em->getConnection()->getConfiguration()->setSQLLogger(null);

        // Initialize some arrrays
        $platforms = array();

        // If the --list switch was used, just list the enabled platforms names
        if ($input->getOption('list')) {
            foreach ($platformServices as $platform) {
                $output->writeln('<info>' . $platform . '</info>');
            }
        } else {
            // If we get an argument, replace the platformServices array with one containing just that plaform
            if ($platform = $input->getArgument('platform')) {
                 $platformServices = array($platform);
            }

            // Iterate on the enabled platforms to retrieve the actual object
            foreach ($platformServices as $platform) {
                // Store the platform object
                $platforms[$platform] = $container->get($platform);
            }

            // Process each platform :
            //  - get successful crawls from the databse
            //  - parse the corresponding files
            foreach ($platforms as $name => $platform) {
                error_log('[Analysis] Beginning to process ' . $platform->getName());
                // Load the portal object from the database
                $portal = $platform->loadPortal();
                // Cache the platform path
                $platformPath = $dataPath . $name . '/';

                $count = 0;
                $total = count(glob($platformPath . '*')) - 1;
                $codes = array();

                if ($dh = @opendir($platformPath)) {
                    while (($file = readdir($dh)) !== false) {
                        $data = json_decode(file_get_contents($platformPath . $file), true);

                        if (null != $data && array_key_exists('meta', $data)) {
                            $code = $data['meta']['code'];
                            if (!is_int($code)) {
                                $code = 'timeout';
                            }
                            $codes[$code] = (array_key_exists($code, $codes)) ? $codes[$code] + 1 : 1;

                            if (200 == $code) {
                                $count++;
                                $dataset = new \Odalisk\Entity\Dataset();
                                $dataset->setUrl($data['meta']['url']);
                                $dataset->setPortal($portal);
                                $platform->parseFile($data['content'], $dataset);

                                $em->persist($dataset);
                                $dataset = null;

                                if (0 == ($count % 100) || $count == $total) {
                                   error_log('[Analysis] ' . $count . ' / ' . $total . ' done');
                                   error_log('[Analysis] currently using ' . memory_get_usage(true) / (1024 * 1024) . 'MB of memory');
                                }
                            }
                        }
                        $data = null;
                    }
                    closedir($dh);
                } else {
                    error_log('[Analysis] nothing to be done. Perhaps ./console odalisk:crawl ' . $name);
                    continue;
                }

                error_log('[Analysis] ' . $count . ' / ' . $total . ' done');
                error_log('[Analysis] ' . ($total - $count) . ' datasets failed to download' . "\n");
                error_log('[Analysis] Return codes repartition :');
                foreach ($codes as $code => $count) {
                   error_log('[Analysis] ' . $code . ' > ' . $count);
                }
                error_log('[Analysis] Persisting data to the database');
                $em->flush();
            }
        }
        $end = time();
        error_log('[Analysis] Processing ended after ' . ($end - $start) . ' seconds');
    }
}
