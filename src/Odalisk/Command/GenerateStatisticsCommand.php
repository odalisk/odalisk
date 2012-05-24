<?php

namespace Odalisk\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;


use Odalisk\Entity\Dataset;
use Odalisk\Entity\Statistics;
use Odalisk\Entity\DatasetCriteria;
use Odalisk\Entity\Metric;
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
        $criteriaRepo->init();
        $criteriaRepo->clear();
        // This make us capable to iterate on all datasets without load them in one
        // shot.
        $sql = 'SELECT d.id FROM Odalisk\Entity\Dataset d';
        $datasetsIds = $this->em->createQuery($sql)->iterate();

        $this->writeBlock($output, "[Statistics] Beginning of generation");
        foreach($datasetsIds as $i => $datasetId) {
            $datasetId = $datasetId[$i]['id'];
            $dataset   = $datasetRepo->find($datasetId);
            $criteria  = $criteriaRepo->getCriteria($dataset);

            $datasetCriteria = new DatasetCriteria();
            foreach($criteria as $name => $value) {
                call_user_func(array($datasetCriteria ,$name), $value);
            }

            $dataset->setCriteria($datasetCriteria);
            $this->em->persist($datasetCriteria);

            $i++;
            if($i % 100 == 0) {
                $this->em->flush();
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
        $portalCriteriaRepo = $this->getEntityRepository('Odalisk\Entity\PortalCriteria');

        foreach($portals as $portal) {

            $avgs = $criteriaRepo->getPortalAverages($portal);
            $portalcriteria = $portalCriteriaRepo->getPortalCriteria($portal);

            foreach($metrics as $name => $category) {
                $value = 0;
                switch($name) {
                    case 'cataloging' :
                        $metric_parent = new \Odalisk\Entity\Metric();
                        $metric_parent->setName('cataloging', $avgs);
                        $metrics = $this->apply_section('cataloging',$category,$avgs);
                        foreach ($metrics as $metric) {
                            $metric_parent->addMetric($metric);
                            $value += $section['weight'] * $metric->getScore();
                            $metric->setParent($metric_parent);
                        }
                        $metric_parent->setCoefficient($category['weight']);
                        $metric_parent->setScore($value);
                        $this->em->persist($metric_parent);
                    break;

                    default:
                        $metric_parent = $this->apply_section($name,$category,$portalcriteria);
                        $metric_parent->setCoefficient($category['weight']);
                        $this->em->persist($metric_parent);
                    break;
                }
            }
        }
        $this->em->flush();
        $this->writeBlock($output, "[Statistics] The end !");
    }

    
    protected function apply_section($name, $criteria, $avgs){
        if (isset($criteria['sections'])) {
            $value = 0;
            $metric_parent = new \Odalisk\Entity\Metric();
            $metric_parent->setName($name);

            foreach ($criteria['sections'] as $name => $section) {
                $metric = $this->apply_section($name, $section, $avgs);
                $value += $metric->getScore();
                $metric->setParent($metric_parent);
                $this->em->persist($metric);
                $metric_parent->addMetric($metric);
            }
            $metric_parent->setCoefficient($criteria['weight']);
            $metric_parent->setScore($criteria['weight'] * $value);
            $this->em->persist($metric_parent);
            return $metric_parent;
        } else {
            $metric = new \Odalisk\Entity\Metric();
            $metric->setScore($criteria['weight'] * $avgs[$name]);
            $metric->setDescription($criteria['description']);
            $metric->setCoefficient($criteria['weight']);
            $metric->setName($name);
            return $metric;
        }
    }
}
