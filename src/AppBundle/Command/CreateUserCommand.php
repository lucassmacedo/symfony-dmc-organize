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

class CreateUserCommand extends Command
{
    private $folderFrom = '/media/lucas/1EC4EE21C4EDFB43/Users/LucasDesk/Desktop/DMD/Sem\ arumar/Tmp';

    private $folderTo   = '/media/lucas/1EC4EE21C4EDFB43/Users/LucasDesk/Desktop/DMD/programa/';

    private $autor      = 'Daiane Luna';

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
        ->setName('app:photo')// the short description shown while running "php bin/console list"
        ->setDescription('Creates a new user.')// the full command description shown when running the command with
        // the "--help" option
        ->setHelp('This command allows you to create a user...');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $finder = new Finder();
        $finder->in($this->folderFrom);
        $fs = new Filesystem();

        $countFolders = 0;
        foreach ($finder->directories() as $directory) {
            $countFolders++;
            $singFolder = new Finder();

            $singFolder->in($directory->getRealPath());

            $bundle = explode(' ', $directory->getRelativePathname(), 2);


            if (!$this->validateDate($bundle[0])) {
                echo $directory->getRelativePathname() . ' Não coresponde ao formato dd-mm-yy';
                continue;
            }

            $helper = $this->getHelper('question');

            $date = $bundle[0];
            $nameFolder = $directory->getRelativePathname();

            if (! $this->validateDate($bundle[0])) {
                continue;
            }

            $date = explode('-', $bundle[0]);

            $directoryTo = $this->folderTo.$date[2]."/".$this->months[$date[1]]."/".$date['2'].'_'.$date['1'].'_'.$date['0'];
            if (isset($bundle[1])) {
                $directoryTo = $directoryTo.' - '.$bundle[1];
            }

            if (! $fs->exists($directoryTo)) {
                try {
                    $fs->mkdir($directoryTo);
                } catch(IOExceptionInterface $e) {
                    echo "Erro ao criar diretório ".$e->getPath();
                }
            }   

            $number = 0;
            $progress = new ProgressBar($output, $singFolder->files()->count());

            foreach ($singFolder->files() as $file) {

                $number = sprintf('%1$05d', ++$number);

                $name = $date['2'].'.'.$date['1'].'.'.$date['0'].'_'.$this->autor.'_'.$number;

                $imageTo = $directoryTo.'/'.$name.'.'.$file->getExtension();

                $optimizerChain = OptimizerChainFactory::create();
                $optimizerChain->optimize($file->getPathName(), $imageTo);

                // if ($file->getExtension() == "JPG") {
                //     $resource = imagecreatefromjpeg($file);
                //     imagejpeg($resource, $imageTo, 70);
                // }
                // if ($file->getExtension() == "PNG") {
                //     $resource = imagecreatefrompng($file);
                //     imagejpeg($resource, $imageTo, 70);
                // }

                $progress->advance();
            }
            $progress->finish();

        }

        echo "\n" . $countFolders . " pastas organizadas \n";
    }

    private function makeDirectories()
    {
    }

    private function validateDate($date, $format = 'd-m-Y')
    {
        $d = \DateTime::createFromFormat($format, $date);

        return $d && $d->format($format) == $date;
    }
}