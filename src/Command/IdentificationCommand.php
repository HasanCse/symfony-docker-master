<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class IdentificationCommand extends Command
{
    protected static $defaultName = 'identification-requests:process';

    protected function configure()
    {
        $this->addArgument('file_name', InputArgument::REQUIRED, 'Which file you want to process?');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
    $output->writeln(\App\Controller\Validator::index($output));
    
    }
}
