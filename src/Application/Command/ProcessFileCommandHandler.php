<?php


namespace App\Application\Command;


use App\Domain\Entity\File;
use App\Domain\Entity\Station;
use App\Infrastructure\Doctrine\Repository\FileRepository;
use App\Infrastructure\Doctrine\Repository\StationRepository;
use App\Shared\Domain\Bus\Command\CommandHandler;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ProcessFileCommandHandler implements CommandHandler
{
    private $fileRepository;
    private $stationRepository;
    private $entityManager;
    private $params;

    public function __construct(FileRepository $fileRepository, StationRepository $stationRepository, EntityManagerInterface $entityManager, ParameterBagInterface $params)
    {
        $this->fileRepository = $fileRepository;
        $this->stationRepository = $stationRepository;
        $this->entityManager = $entityManager;
        $this->params = $params;
    }

    public function __invoke(ProcessFileCommand $command)
    {
        if (isset($id)) {
            $file = $this->fileRepository->find($command->id());
            if (!$file) {
                //$io->error('File not found: ' . $id);
                return;
            }
        } else {
            $file = current($this->fileRepository->findBy([], ['id' => 'desc'], 1));
            if (!$file) {
                //$io->error('No one file found: ' . $id);
                return;
            }
        }

        // Comprobamos si el fichero esta activo, esto indicaría que no hemos conseguido traer ningún fichero mas por lo que no procesamos el archivo
        if ($file->getActive()) {
            //$io->success('El fichero ya había sido procesado');
        } else {
            $this->processFile($file);
            //$io->success('Execution succeed');
        }
    }

    private function processFile(File $file)
    {
        $nameFile = $file->getFile();
        $pathFile = $this->params->get('download_save_path') . '/' . $nameFile;
        $spreadsheet = IOFactory::load($pathFile);
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        $this->entityManager->getConnection()->getConfiguration()->setSQLLogger(null);

        // Borro las estaciones para empezar de nuevo
        /*
        $qb = $this->entityManager->createQueryBuilder();
        $qb->delete('App\Domain\Entity\Station', 's')
            ->where('s.file = :id')
            ->setParameter('id', $file->getId());
        $qb->getQuery()->execute();
        $this->entityManager->flush();
        */
        $this->fileRepository->deleteStationsFromFile($file);

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

            $this->stationRepository->save($station);
            /*
            $this->entityManager->persist($station);
            if (($i++ % 50) == 0) {
                $this->entityManager->flush();
            }
            */
            unset($station);
            unset($item);
        }
        //$this->entityManager->flush();

        // Marco los antiguos como NO válidos
        /*
        $qb = $this->entityManager->createQueryBuilder();
        $qb->update('App\Domain\Entity\File', 'f')
            ->set('f.active', 0);
        $qb->getQuery()->execute();
        */
        $this->fileRepository->inactiveAllFiles();

        // Marco el nuevo como válido
        $file->setActive(true);

        $this->fileRepository->save($file);
    }

    private function formatFloat($value) {
        return floatval(str_replace(',','.', $value));
    }
}