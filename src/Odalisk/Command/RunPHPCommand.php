<?php
namespace Odalisk\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;

class RunPHPCommand extends BaseCommand
{
    protected $categories = array();
    protected $aliases = array();
    
    protected function configure()
    {
        $this
            ->setName('run:php')
            ->setDescription('Analyse HTML for all supported platforms');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        
        $dir = '/Users/bowbaq/Dropbox/projet/odalisk/src/Odalisk/Resources/config';
        $data = file_get_contents($dir . '/categories.yml');

        $yaml = new Parser();

        try {
            $value = $yaml->parse($data);
            foreach($value['categories'] as $category => $data) {
                $c = new \Odalisk\Entity\Category($category);
                foreach($data['aliases'] as $alias) {
                    $c->addAlias($alias);
                    $this->aliases[$alias] = $category;
                }
                $this->categories[$category] = $c;
            }
            
            var_dump($this->categories);
        } catch (ParseException $e) {
            printf("Unable to parse the YAML string: %s", $e->getMessage());
        }
    }

}