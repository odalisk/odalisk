<?php

namespace Odalisk\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Input\InputInterface;


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
            ->setDescription('generate statistics from datasets');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getEntityManager();
        $datasetRepo = $this->getEntityRepository('Odalisk\Entity\Dataset');
        $criteriaRepo = $this->getEntityRepository('Odalisk\Entity\DatasetCriteria');

        // Initialization
        $criteriaRepo->init();
        // This make us capable to iterate on all datasets without load them in one
        // shot.
        $datasetsIds = $this->em->createQuery('SELECT d.id FROM Odalisk\Entity\Dataset d')->iterate();
        $datasetsCount = count($datasetsIds);

        $this->writeBlock($output, "[Statistics] Beginning of generation");

        foreach($datasetsIds as $i => $datasetId) {
            $datasetId = $datasetId[$i]['id'];
            $dataset = $datasetRepo->find($datasetId);
            $criteria = $criteriaRepo->getCriteria($dataset);

            $datasetCriteria = new DatasetCriteria();
            foreach($criteria as $name => $value) {
                call_user_func(array($datasetCriteria ,$name), $value);
            }

            $dataset->setCriteria($datasetCriteria);
            $this->em->persist($datasetCriteria);

            $i++;
            if($i % 100 == 0) {
                $this->em->flush();
                error_log("[Statistics] flush $i datasets' criteria on $datasetsCount");
            }
        }
        $this->em->flush();
        error_log("[Statistics] flush $i datasets' criteria on $datasetsCount");

        $this->writeBlock($output, "[Statistics] The end !");
    }
}
