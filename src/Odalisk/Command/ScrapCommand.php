<?php

namespace Odalisk\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

/**
 * A base abstract command that provides shortcuts to some useful tools for scrapping
 */
abstract class ScrapCommand extends ContainerAwareCommand {
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
