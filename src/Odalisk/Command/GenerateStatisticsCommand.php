<?php

namespace Odalisk\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Symfony\Component\DomCrawler\Crawler;

use Odalisk\Entity\Statistics;

use Buzz\Browser;

/**
 * Generates statistics
 */
class GenerateStatisticsCommand extends ContainerAwareCommand {
    
    /**
     * Holds our instance of the EntityManager
     *
     * @var $em
     */
    private $em;
        
    private $formatter = NULL;

    private $stats = array();
    
    protected function configure(){
        $this
            ->setName('odalisk:statistics:generate')
            ->setDescription('generate statistics from datasets')
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->writeBlock($output, "Generating stats");
        $this->em = $this->getContainer()
                            ->get('doctrine')->getEntityManager();

        $repository = $this->getContainer()
                            ->get('doctrine')
                            ->getRepository('Odalisk\Entity\Portal')
                            ;
        $portals = $repository->findAll();

        $this->em->getConnection()->prepare('TRUNCATE statistics;')->execute();
        $this->em->flush();

        $stats_repository = $this->getContainer()
                            ->get('doctrine')
                            ->getRepository('Odalisk\Entity\Statistics')
                            ;

        

        foreach($portals as $portal){
            echo $portal->getName()."\n";
            
            $stats = new Statistics();
            $stats->setPortal($portal);
            $stats->setDatasetsCount($stats_repository->getDatasetsCount($portal));
            $stats->setInChargePersonCount($stats_repository->getInChargePersonCount($portal));
            $stats->setReleasedOnCount($stats_repository->getReleasedOnExistCount($portal));
            $stats->setLastUpdatedOnCount($stats_repository->getLastUpdatedOnExistCount($portal));
            $stats->setCategoryCount($stats_repository->getCategoryExistCount($portal));
            $stats->setSummaryAndTitleCount($stats_repository->getSummaryAndTitleAtLeastCount($portal));
            $this->em->persist($stats);
            $this->em->flush();
        }
        
        $this->writeBlock($output, "End of generating"); 
    }

    protected function writeBlock(OutputInterface $output, $message) {
        if(NULL == $this->formatter) {
            $this->formatter = new FormatterHelper();
        }
        
        $output->writeln($this->formatter->formatBlock(
                $message,
                'bg=blue;fg=white',
                TRUE
            )
        );
    }
    
    protected function collectStats($data) {
        if(isset($this->stats[$data['code']])) {
            $this->stats[$data['code']] += 1;
        } else {
            $this->stats[$data['code']] = 1;
        }
    }
    
    protected function printStats(OutputInterface $output) {
        $output->writeln('<info>HTTP return code distribution : </info>');
        foreach($this->stats as $code => $count) {
            $output->writeln("<comment>[$code]</comment> => " . $count);
        }
    }
    
    protected function getBuzz() {
        if(NULL == $this->buzz) {
            $this->buzz = $this->getContainer()->get('buzz');
        }

        return $this->buzz;
    }
    
    protected function getEntityManager($managerName = NULL) {
        if(NULL == $this->em) {
            $this->em = $this->getContainer()->get('doctrine')->getEntityManager($managerName);
        }
        return $this->em;
    }

    protected function getEntityRepository($repositoryName, $managerName = NULL) {
        return $this->getEntityManager($managerName)->getRepository($repositoryName);
    }
}
