<?php

namespace Odalisk\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Odalisk\Entity\Portal;

class PersistPortalsCommand extends ContainerAwareCommand {

	private $repo = NULL;
	private $em   = NULL;

	private $portalPath = 'Odalisk\Portals\\';

	protected function initialize(InputInterface $input, OutputInterface $output) {
		parent::initialize($input, $output);
		$this->em   = $this->getContainer()->get('doctrine')->getEntityManager();
		$this->repo = $this->em->getRepository('Odalisk\Entity\Portal');
	}

    protected function configure(){
		parent::configure();
        $this
            ->setName('odalisk:persist:portal')
            ->setDescription('Stores a portal in database')
			->addArgument('class_name', InputArgument::REQUIRED, 'The name of the class of the portal to persist')
			->addOption('update', 'u', InputOption::VALUE_OPTIONAL, 'Update the portal')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
		$className = $input->getArgument('class_name');
		$classPath = $this->portalPath.$className;
		$portal    = new $classPath();

		$exist = $this->repo->findOneByName($portal->getName());
		if($exist) {
			if($input->getOption('update')) {
				$newEntity = $portal->getNewEntity();
				$exist->setName($newEntity->getName());
				$exist->setUrl($newEntity->getUrl());
				$exist->setBaseUrl($newEntity->getBaseUrl());
				$exist->setClassName($className);
				$this->em->flush();
				$output->writeln('<info>Portal updated !</info>');
			} else {
				$output->writeln('<info>The portal is already stored.</info>');
			}
		} else {
			$this->em->persist($portal->getNewEntity());
			$this->em->flush();
			$output->writeln('<info>The portal is now stored !</info>');
		}
	}

	protected function getRepo() {
        if(NULL == $this->repo) {
            $this->repo = $this->em->getRepository('Odalisk\Entity\Portal');
        }
        return $this->repo;
	}

	/*
	protected function getEM() {
        if(NULL == $this->em) {
            $this->em = $this->getContainer()->get('doctrine')->getEntityManager();
        }
        return $this->repo;
	}
	*/
}
