<?php
namespace Odalisk\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Yaml\Dumper;

class RunPHPCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('run:php')
            ->setDescription('Analyse HTML for all supported platforms');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dir = '/Users/bowbaq/Dropbox/projet/odalisk/src/Odalisk/Resources/config';
        $json = file_get_contents('/Users/bowbaq/Dropbox/projet/odalisk/src/Odalisk/Resources/fileFormat.json');
        var_dump($json);
        $formats = json_decode($json);

        $dumper = new Dumper();
        $yaml = $dumper->dump($formats);
        
        file_put_contents($dir . '/format.yml', $yaml);
    }
}