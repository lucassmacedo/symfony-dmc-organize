<?php

namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Helper\ProgressBar;
use Spatie\ImageOptimizer\OptimizerChainFactory;
class TestImageCommand extends Command
{
    private $folderFrom = '/media/lucas/Dados/DMC/DMC/';

    private $folderTo   = '/home/lucas/Área de Trabalho/03-08/test/';


    protected function configure()
    {
        $this// the name of the command (the part after "bin/console")
        ->setName('app:test')// the short description shown while running "php bin/console list"
        ->setDescription('Creates a new user.')// the full command description shown when running the command with
        // the "--help" option
        ->setHelp('This command allows you to create a user...');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $finder = new Finder();
        $finder->in($this->folderFrom);

        $progress = new ProgressBar($output, $finder->files()->count());

        foreach ($finder->files() as $key => $value) {

            $progress->advance();
            $optimizerChain = OptimizerChainFactory::create();
            $optimizerChain->optimize($value->getPathName());
            // $optimizerChain->optimize($value->getPathName(), $this->folderTo . $value->getFileName());

        }

        $progress->finish();


        // $optimizerChain = OptimizerChainFactory::create();
        // dump($optimizerChain);
    }
}