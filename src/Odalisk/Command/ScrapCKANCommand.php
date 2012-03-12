<?php

namespace Odalisk\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\CssSelector\CssSelector;

/**
 * A command that will scrap data from the CKAN portal
 */
class ScrapCKANCommand extends ScrapCommand {
    private $xpaths = array(
        "Owner" => '.separator',
        /*
        "Update Frequency" => '//div[@class="tx_icsoddatastore_pi1_updatefrequency separator"]/span[@class="value"]',
        "Date of publication" => '//div[@class="tx_icsoddatastore_pi1_releasedate separator"]/span[@class="value"]',
        "Last update" => '//div[@class="tx_icsoddatastore_pi1_updatedate separator"]/span[@class="value"]',
        "Description" => '//div[@class="tx_icsoddatastore_pi1_description separator"]/span[@class="value"]',
        "Technical data" => '//div[@class="tx_icsoddatastore_pi1_technical_data separator"]/span[@class="value"]',
        */
    );
    
    protected function configure(){
        $this
            ->setName('odalisk:scrap:nantes')
            ->setDescription('Fetch some data from ckan.org')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $output->writeln('<info>This is a demo command fetching some data</info>');
        
        $response = $this->getBuzz()->get(
            'http://data.nantes.fr/donnees/detail/?' . urlencode('tx_icsoddatastore_pi1[uid]=14'),
            array(
                'User-agent: Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.0.1) Gecko/2008071615 Fedora/3.0.1-1.fc9 Firefox/3.0.1',
            )
        );
        
        if(200 == $response->getStatusCode()) {
            $html = $response->getContent();
            $output->writeln($html);
            $crawler = new Crawler($html);
            
            //$output->writeln($crawler->text());
            
            foreach($this->xpaths as $criteria => $path) {
                $data = $crawler->filter($path);
                $output->writeln($path);
                $output->writeln(print_r($data, TRUE));
            }
        } else {
            $output->writeln('<error>Oups! We couldn\'t fetch the data. Got status code ' . $response->getStatusCode() . '</error>');
        }
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
}