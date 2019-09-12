<?php

namespace App\Command;

use App\Entity\File;
use App\Entity\Station;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ProcessExcelCommand extends Command
{
    protected static $defaultName = 'app:process-excel';

    private $entityManager;
    private $params;

    public function __construct(EntityManagerInterface $entityManager, ParameterBagInterface $params)
    {
        $this->entityManager = $entityManager;
        $this->params = $params;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            // ...
            ->addArgument('id', InputArgument::OPTIONAL, 'Id of the Excel to process')
        ;

        $this
            ->setDescription('Process Excel from Database')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $id = $input->getArgument('id');

        if (isset($id)) {
            $file = $this->entityManager->getRepository(File::class)->find($id);
            if (!$file) {
                $io->error('File not found: ' . $id);
                return;
            }
        } else {
            $file = current($this->entityManager->getRepository(File::class)->findBy([], ['id' => 'desc'], 1));
            if (!$file) {
                $io->error('No one file found: ' . $id);
                return;
            }
        }

        // Comprobamos si el fichero esta activo, esto indicaría que no hemos conseguido traer ningún fichero mas por lo que no procesamos el archivo
        if ($file->getActive()) {
            $io->success('El fichero ya había sido procesado');
        } else {
            $this->processFile($file);
            $io->success('Execution succeed');
        }
    }

    private function processFile($file) {
        $nameFile = $file->getFile();
        $pathFile = $this->params->get('download_save_path') . '/' . $nameFile;
        $spreadsheet = IOFactory::load($pathFile);
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        $this->entityManager->getConnection()->getConfiguration()->setSQLLogger(null);

        // Borro las estaciones para empezar de nuevo
        $qb = $this->entityManager->createQueryBuilder();
        $qb->delete('App\Entity\Station', 's')
            ->where('s.file = :id')
            ->setParameter('id', $file->getId());
        $qb->getQuery()->execute();
        $this->entityManager->flush();

        // Inserto las estaciones del Excel
        $i = 0;
        $sheetData = array_splice($sheetData, 4);
        foreach ($sheetData as $item) {
            $station = new Station();
            $station->setProvince($item['A']);
            $station->setMunicipality($item['B']);
            $station->setLocation($item['C']);
            $station->setPostalCode($item['D']);
            $station->setAddress($item['E']);
            $station->setLng($this->formatFloat($item['G']));
            $station->setLat($this->formatFloat($item['H']));
            $station->setPriceGasoline95($this->formatFloat($item['I']));
            $station->setPriceDieselA($this->formatFloat($item['J']));
            $station->setPriceDieselB($this->formatFloat($item['K']));
            //$station->setPriceBioethanol($this->formatFloat($item['L']));
            //$station->setPriceNewDieselA($this->formatFloat($item['M']));
            //$station->setPriceBiodiesel($this->formatFloat($item['N']));
            $station->setPriceGasoline98($this->formatFloat($item['Q']));
            //$station->setPriceCompressedNaturalGas($this->formatFloat($item['R']));
            //$station->setPriceLiquefiedNaturalGas($this->formatFloat($item['S']));
            //$station->setPriceLiquefiedPetroleumGas($this->formatFloat($item['T']));
            $station->setLabel($item['U']);
            $station->setSaleType($item['V']);
            $station->setRem($item['W']);
            $station->setSchedule($item['X']);
            $station->setFile($file);

            $this->entityManager->persist($station);
            if (($i++ % 50) == 0) {
                $this->entityManager->flush();
            }
            unset($station);
            unset($item);
        }
        $this->entityManager->flush();

        // Marco los antiguos como NO válidos
        $qb = $this->entityManager->createQueryBuilder();
        $qb->update('App\Entity\File', 'f')
            ->set('f.active', 0);
        $qb->getQuery()->execute();

        // Marco el nuevo como válido
        $file->setActive(true);

        $this->entityManager->persist($file);
        $this->entityManager->flush();
    }

    private function formatFloat($value) {
        return floatval(str_replace(',','.', $value));
    }
}
