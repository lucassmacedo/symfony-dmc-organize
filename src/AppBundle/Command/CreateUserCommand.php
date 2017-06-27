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

class CreateUserCommand extends Command
{
    private $folderFrom = '/Users/Lucas/Downloads/drive-download-20170627T170713Z-001/';

    private $folderTo   = '/Users/Lucas/projetos/symfonyprojects/testing/testing/';

    private $months     = [
        '01' => '01_janeiro',
        '02' => '02_fevereiro',
        '03' => '03_marco',
        '04' => '04_abril',
        '05' => '05_maio',
        '06' => '06_junho',
        '07' => '07_julho',
        '08' => '08_agosto',
        '09' => '09_setembro',
        '10' => '10_outubro',
        '11' => '11_novembro',
        '12' => '12_dezembro',
    ];

    protected function configure()
    {
        $this// the name of the command (the part after "bin/console")
        ->setName('app:create-user')// the short description shown while running "php bin/console list"
        ->setDescription('Creates a new user.')// the full command description shown when running the command with
        // the "--help" option
        ->setHelp('This command allows you to create a user...');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $finder = new Finder();
        $finder->in($this->folderFrom);
        $fs = new Filesystem();
        foreach ($finder->directories() as $directory) {

            $singFolder = new Finder();

            $singFolder->in($this->folderFrom);

            $helper = $this->getHelper('question');

            $question = new Question('Pasta  - '.$directory->getRelativePathname().': ');

            $bundle = $helper->ask($input, $output, $question);

            if (! $this->validateDate($bundle)) {
                continue;
            }
            $date = explode('/', $bundle);

            $directoryTo = $this->folderTo.$date[2]."/".$this->months[$date[1]];

            if (!$fs->exists($directoryTo)) {
                try {
                    $fs->mkdir($directoryTo);
                } catch(IOExceptionInterface $e) {
                    echo "Erro ao criar diretório ".$e->getPath();
                }
            }

            foreach ($singFolder->files()->in($directory->getRealPath()) as $file) {
                $fs->copy($file, 'image.jpg');
             
                die();
            }
        }
    }

    private function makeDirectories()
    {
    }

    private function validateDate($date, $format = 'd/m/Y')
    {
        $d = \DateTime::createFromFormat($format, $date);

        return $d && $d->format($format) == $date;
    }
}