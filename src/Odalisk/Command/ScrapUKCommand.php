<?php

namespace Odalisk\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\FormatterHelper;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\CssSelector\CssSelector;

use Odalisk\Scraper\UK\UkPortal;

/**
 * A command that will scrap data from the Nantes' portal
 */
class ScrapUkCommand extends ScrapCommand {
    protected function configure(){
        $this
            ->setName('odalisk:scrap:uk')
            ->setDescription('Fetch some data from data.gov.uk')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $formatter = new FormatterHelper();
        $this->writeBlock($output, $formatter, 'Scraping data.gov.uk');
        
        $portal = new UkPortal($this->getBuzz());
        $count = 0;
        $start = time();
        
        foreach($portal->getDatasets() as $dataset) {
             $output->writeln('<info>' . $dataset->url . '</info>');
             if(!$dataset->fetch()) {
                 $dataset->fetch(15);
             }
             if(!$dataset->isEmpty()) {
                if($dataset->parse()) {
                    ++$count;
                    if(0 == $count % 50) {
                        $this->writeBlock($output, $formatter, 'Scraped ' . $count . ' datasets in ' . (time() - $start) . ' seconds.');
                    }
                    $criteria = $dataset->getData();
                    foreach($criteria as $criterion => $value) {
                        $output->writeln("<info>[$criterion]</info> " . $value);
                    }
                } else {
                    $output->writeln('<error>Got empty page</error>');
                }
             } else {
                 $output->writeln('<error>Fetch error</error>');
             }
        }
        $end = time();
        
        $this->writeBlock($ouptut, $formatter, 'Scraped ' . $count . ' datasets in ' . ($end - $start) . ' seconds.');
    }
    
    public function writeBlock(OutputInterface $output, FormatterHelper $formatter, $message) {
        $output->writeln($formatter->formatBlock(
                $message,
                'bg=blue;fg=white',
                TRUE
            )
        );
    }
}