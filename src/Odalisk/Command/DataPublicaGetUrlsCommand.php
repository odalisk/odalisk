<?php

namespace Odalisk\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Symfony\Component\DomCrawler\Crawler;

use Buzz\Browser;

/**
 * A base abstract command that provides shortcuts to some useful tools for scrapping
 */
class DataPublicaGetUrlsCommand extends ContainerAwareCommand {
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
    
    private $formatter = NULL;
    
    private $stats = array();
    
    protected function configure(){
        $this
            ->setName('odalisk:getUrls:datapublica')
            ->setDescription('Get urls from DataPublica')
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output) {
        $browser = new Browser();
        $finished = false;
        $i = 0;
        
        $path = "src/Odalisk/Scraper/DataPublica/data_publica_urls.txt";   

        if(file_exists($path) ){
           unlink($path); 
        }
        
        do{            
            echo $i."\n";

            $response = $browser->get("http://www.data-publica.com/search/?page=".$i);
            
            if($response->getStatusCode() == 200){
                $crawler = new Crawler($response->getContent());
                $nodes = $crawler->filterXPath(".//*[@id='content']/article[2]/ol/li/a/@href");
                if(0 < count($nodes)) {
                            $nodes->each(
                                function($node,$i) {
                                      file_put_contents("src/Odalisk/Scraper/DataPublica/data_publica_urls.txt", "http://www.data-publica.com".$node->nodeValue."\n",FILE_APPEND);
                                 }
                              );
                           
                }
                else{
                    $finished = true;
                }
            }
            $i++;
        }while(!$finished);


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
