<?php

namespace Odalisk\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * A base abstract command that provides shortcuts to some useful tools for scrapping
 */
abstract class BaseCommand extends ContainerAwareCommand
{
    /**
     * Holds the instance of buzz we use to GET the data from the website
     *
     * @var $buzz
     */
    private $buzz;

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
            ->setName('odalisk:' . strtolower(get_class($this)))
            ->setDescription('Not yet implemented');
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
