<?php

namespace Odalisk\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Input\InputInterface;


use Odalisk\Entity\Statistics;


/**
 * Generates statistics
 */
class GenerateStatisticsCommand extends ContainerAwareCommand
{
    /**
     * Holds our instance of the EntityManager
     *
     * @var $em
     */
    private $em;

    private $formatter = null;

    private $stats = array();

    protected function configure()
    {
        $this
            ->setName('odalisk:statistics:generate')
            ->setDescription('generate statistics from datasets');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->writeBlock($output, "Generating stats");
        $this->em = $this->getContainer()
            ->get('doctrine')->getEntityManager();

        $repository = $this->getContainer()
            ->get('doctrine')
            ->getRepository('Odalisk\Entity\Portal');
        $portals = $repository->findAll();

        $this->em->getConnection()->prepare('TRUNCATE statistics;')->execute();
        $this->em->flush();

        $statsRepository = $this->getContainer()
            ->get('doctrine')
            ->getRepository('Odalisk\Entity\Portal');



        foreach ($portals as $portal) {
            echo $portal->getName()."\n";

            $stats = new Statistics();
            $stats->setPortal($portal);
            $stats->setDatasetsCount($statsRepository->getDatasetsCount($portal));
            $stats->setInChargePersonCount($statsRepository->getInChargePersonCount($portal));
            $stats->setReleasedOnExistCount($statsRepository->getReleasedOnExistCount($portal));
            $stats->setLastUpdatedOnExistCount($statsRepository->getLastUpdatedOnExistCount($portal));
            $stats->setCategoryExistCount($statsRepository->getCategoryExistCount($portal));
            $stats->setSummaryAndTitleCount($statsRepository->getSummaryAndTitleAtLeastCount($portal));
            $stats->setLicenseCount($statsRepository->getlicenseCount($portal));
            $this->em->persist($stats);
            $this->em->flush();
        }

        $this->writeBlock($output, "End of generating");
    }

    protected function writeBlock(OutputInterface $output, $message)
    {
        if (null == $this->formatter) {
            $this->formatter = new FormatterHelper();
        }

        $output->writeln($this->formatter->formatBlock(
            $message,
            'bg=blue;fg=white',
            true
        ));
    }

    protected function collectStats($data)
    {
        if (isset($this->stats[$data['code']])) {
            $this->stats[$data['code']] += 1;
        } else {
            $this->stats[$data['code']] = 1;
        }
    }

    protected function printStats(OutputInterface $output)
    {
        $output->writeln('<info>HTTP return code distribution : </info>');
        foreach ($this->stats as $code => $count) {
            $output->writeln("<comment>[$code]</comment> => " . $count);
        }
    }

    protected function getBuzz()
    {
        if (null == $this->buzz) {
            $this->buzz = $this->getContainer()->get('buzz');
        }

        return $this->buzz;
    }

    protected function getEntityManager($managerName = null)
    {
        if (null == $this->em) {
            $this->em = $this->getContainer()->get('doctrine')->getEntityManager($managerName);
        }

        return $this->em;
    }

    protected function getEntityRepository($repositoryName, $managerName = null)
    {
        return $this->getEntityManager($managerName)->getRepository($repositoryName);
    }
}
