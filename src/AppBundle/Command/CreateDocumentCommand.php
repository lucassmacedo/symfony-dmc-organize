<?php

namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Helper\ProgressBar;
use Spatie\ImageOptimizer\OptimizerChainFactory;

class CreateDocumentCommand extends Command
{
    private $folderFrom = '/media/lucas/DMC - Bkp/Sem arumar/Documentos/';

    private $folderTo = '/home/lucas/Área de Trabalho/DMC Organizado/Documentos';

    private $autor = 'Lucas Macedo';

    private $months = [
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
        $this->setName('app:documento')
            ->setDescription('Creates a new user.')
            ->addArgument('tipo', InputArgument::OPTIONAL, 'tipo')
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


//            if (!$this->validateDate($bundle[0])) {
//                echo $directory->getRelativePathname() . ' Não coresponde ao formato dd-mm-yy';
//                continue;
//            }

//            $helper = $this->getHelper('question');
//
//            $date = $bundle[0];
//            $nameFolder = $directory->getRelativePathname();
//
//            if (!$this->validateDate($bundle[0])) {
//                continue;
//            }
//
//            $date = explode('-', $bundle[0]);
//
//            $directoryTo = $this->folderTo . $date[2] . "/" . $this->months[$date[1]] . "/" . $date['2'] . '_' . $date['1'] . '_' . $date['0'];
//            if (isset($bundle[1])) {
//                $directoryTo = $directoryTo . ' - ' . $bundle[1];
//            }
//
//            if (!$fs->exists($directoryTo)) {
//                try {
//                    $fs->mkdir($directoryTo);
//                } catch (IOExceptionInterface $e) {
//                    echo "Erro ao criar diretório " . $e->getPath();
//                }
//            }

            $number = 0;
            $progress = new ProgressBar($output, $singFolder->files()->count());

            foreach ($singFolder->files() as $file) {

                $created = date('Y_m_d', $file->getMTime());
                $number = sprintf('%1$05d', ++$number);

                $name = sprintf("%s_U(1) - %s", $created, $file->getFilename());


                $imageTo = $this->folderTo . '/' . $name;


                echo "Copiando pasta " . $directory->getFilename() . " ----  Arquivo: " . $name . " --- Tamanho: " . ($this->formatSizeUnits($file->getSize())) . "\n";

                copy($file->getPathName(), $imageTo);
                // $optimizerChain = OptimizerChainFactory::create();
                // $optimizerChain->optimize($file->getPathName(), $imageTo);

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

    function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }
        return $bytes;
    }

}