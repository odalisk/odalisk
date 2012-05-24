<?php

namespace Odalisk\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;


use Odalisk\Entity\Dataset;
use Odalisk\Entity\Statistics;
use Odalisk\Entity\DatasetCriteria;

/**
 * Generates statistics
 */
class GenerateStatisticsCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('odalisk:statistics:generate')
            ->setDescription('generate statistics from datasets')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getEntityManager();
        $datasetRepo  = $this->getEntityRepository('Odalisk\Entity\Dataset');
        $criteriaRepo = $this->getEntityRepository('Odalisk\Entity\DatasetCriteria');

        // Initialization
        $criteriaRepo->clear();
        // This make us capable to iterate on all datasets without load them in one
        // shot.
        $datasets = $this->em->createQuery('SELECT d FROM Odalisk\Entity\Dataset d')->iterate();
        
        $this->writeBlock($output, "[Statistics] Beginning of generation");
        $i = 0;
        foreach($datasets as $row) {
            $dataset = $row[0];
            $criteria = new DatasetCriteria($criteriaRepo->getCriteria($dataset));
            $dataset->setCriteria($criteria);
            $this->em->persist($criteria);
            $this->em->persist($dataset);

            $i++;
            if($i % 1000 == 0) {
                $this->em->flush();
                $this->em->clear();
                error_log("[Statistics] flush $i datasets' criteria");
            }
        }
        $this->em->flush();
        error_log("[Statistics] flush $i datasets' criteria");

        // Metrics generation
        error_log("[Statistics] Metrics generation");
        $container  = $this->getContainer();
        $metrics    = $container->getParameter('metrics');
        $portalRepo = $this->getEntityRepository('Odalisk\Entity\Portal');
        $portals    = $portalRepo->findAll();

        foreach($portals as $portal) {
            foreach($metrics as $name => $category) {
                switch($name) {
                    case 'cataloging' :
                        $avgs = $criteriaRepo->getPortalAverages($portal->getId());
                        // Get all datasets metrics
                        // Group them
                        // Apply weights
                        // Persits in database (metric table)
                    break;

                    default:
                        error_log("PROUT");
                        // Get data from portal criteria
                        // Apply weights
                        // Persits in database (metric table)
                    break;
                }
            }
        }

        $this->writeBlock($output, "[Statistics] The end !");
    }
}
