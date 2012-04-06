<?php

namespace Odalisk\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\FormatterHelper;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\CssSelector\CssSelector;

use Odalisk\Scraper\Nantes\NantesPortal;

/**
 * A command that will scrap data from the Nantes' portal
 */
class ScrapNantesCommand extends ScrapCommand {
    protected function configure(){
        $this
            ->setName('odalisk:scrap:nantes')
            ->setDescription('Fetch some data from data.nantes.fr')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $formatter = new FormatterHelper();
        $output->writeln($formatter->formatBlock(
                'Scraping data.nantes.fr',
                'bg=blue;fg=white',
                TRUE
            )
        );
        
        $portal = new NantesPortal($this->getBuzz());
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
        
        $output->writeln($formatter->formatBlock(
                'Scraped ' . $count . ' datasets in ' . ($end - $start) . ' seconds.',
                'bg=blue;fg=white',
                TRUE
            )
        );
    }
    /*
    protected function persistMatches(&$teams, $newest) {
        // Assume we are not done persisting yet
        $import_more = TRUE;
        // Get the html out of the data we got
        $html = $this->matches->commands[0]->parameters->content;
        // Get to the interesting part of it
        $crawler = new Crawler($html);
        $table_rows = $crawler->filter('.matches > tbody > tr');
        
        for($i = count($table_rows) - 1; $i >= 0 ; $i--) {
            $cells = $table_rows->eq($i)->children();
            
            // Persist only games that have been played
            if('score' == substr($cells->eq(3)->attr('class'), -5)) {
                // We need this to know if we already have it or not
                $kick_off = \DateTime::createFromFormat('d/m/y', $cells->eq(1)->text());
                $match_id = substr(strchr($table_rows->eq($i)->attr('id'), '-'), 1);
                
                // Persist only matches that we don't have
                if(NULL == $newest || ($kick_off >= $newest['kick_off'] && ! in_array($match_id, $newest['ids']))) {
                    $match = new Match();
                    $match->setHomeTeam(
                        $this->getOrCreateTeam(trim($cells->eq(2)->text()), $teams)
                    );
                    $match->setAwayTeam(
                        $this->getOrCreateTeam(trim($cells->eq(4)->text()), $teams)
                    );
                    $match->setKickOff($kick_off);

                    $scores = explode('-', $cells->eq(3)->text());
                    $match->setHomeTeamScore(trim($scores[0]));
                    $match->setAwayTeamScore(trim($scores[1]));

                    $match->setMatchId($match_id);

                    $this->getEntityManager()->persist($match);
                    
                    // Let the user know it's working
                    $this->output->writeln('    ~> ' . $match->getHomeTeam()->getName() 
                                            . ' - ' . $match->getAwayTeam()->getName()
                                            . ' [' . $scores[0] . ':' . $scores[1] . ']'
                    );
                    $this->matches_count++;
                } else {
                    // We are importing from newer to older, so if on this page there is at least one
                    // match that we already have, we don't need to check out older pages
                    $import_more = FALSE;
                }
            }
            
            $this->getEntityManager()->flush();
        }
        
        return $import_more;
    }
    */
    
    private function normalize($url) {
        return str_replace(']', '%5D', str_replace('[', '%5B', $url));
    }
}
