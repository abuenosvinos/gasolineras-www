<?php


namespace App\Application\Command;


use App\Application\Event\FileWasProcessed;
use App\Domain\Entity\File;
use App\Domain\Entity\Price;
use App\Domain\Entity\Station;
use App\Infrastructure\Doctrine\Repository\FileRepository;
use App\Infrastructure\Doctrine\Repository\PriceRepository;
use App\Infrastructure\Doctrine\Repository\StationRepository;
use App\Shared\Domain\Bus\Command\CommandHandler;
use App\Shared\Domain\Bus\Event\EventBus;
use DateTime;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ProcessFileCommandHandler implements CommandHandler
{
    private $fileRepository;
    private $stationRepository;
    private $priceRepository;
    private $eventBus;

    public function __construct(FileRepository $fileRepository, StationRepository $stationRepository, PriceRepository $priceRepository, ParameterBagInterface $params, EventBus $eventBus)
    {
        $this->fileRepository = $fileRepository;
        $this->stationRepository = $stationRepository;
        $this->priceRepository = $priceRepository;
        $this->params = $params;
        $this->eventBus = $eventBus;
    }

    public function __invoke(ProcessFileCommand $command)
    {
        $id = $command->id();
        if (isset($id)) {
            $file = $this->fileRepository->find($id);
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

        $this->eventBus->notify(new FileWasProcessed(['name' => $file->getName()]));
    }

    private function processFile(File $file)
    {
        $nameFile = $file->getFile();
        $pathFile = $this->params->get('download_save_path') . '/' . $nameFile;
        $spreadsheet = IOFactory::load($pathFile);
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        $numHeaderSheet = 4;
        $totalSheets = count($sheetData) - $numHeaderSheet;

        // Borro las estaciones para empezar de nuevo
        //$this->fileRepository->deleteStationsFromFile($file);

        // Inserto las estaciones del Excel
        $num_processed = $file->getNumProcessed();
        $offset = $numHeaderSheet + $num_processed;
        $limit = 500;

        $sheetData = array_splice($sheetData, $offset, $limit);
        foreach ($sheetData as $item) {

            $num_processed++;

            $price = new Price();
            $price->setPriceGasoline95($this->formatFloat($item['I']));
            $price->setPriceDieselA($this->formatFloat($item['J']));
            $price->setPriceDieselB($this->formatFloat($item['K']));
            $price->setPriceNewDieselA($this->formatFloat($item['M']));
            $price->setPriceGasoline98($this->formatFloat($item['Q']));
            $price->setPriceLiquefiedPetroleumGas($this->formatFloat($item['T']));
            $price->setDate(DateTime::createFromFormat('d/m/Y H:i', $file->getName()));

            if (!$this->isValidPrice($price)) {
                continue;
            }

            $this->priceRepository->save($price);

            $station = $this->stationRepository->findByLatLng($this->formatFloat($item['H']), $this->formatFloat($item['G']));
            if (!$station) {
                $station = new Station();
            }

            $station->setProvince($item['A']);
            $station->setMunicipality($item['B']);
            $station->setLocation($item['C']);
            $station->setPostalCode($item['D']);
            $station->setAddress($item['E']);
            $station->setLng($this->formatFloat($item['G']));
            $station->setLat($this->formatFloat($item['H']));
            $station->setPriceGasoline95($this->formatFloat($item['I']));
            $station->setPriceDieselA($this->formatFloat($item['N']));
            $station->setPriceDieselB($this->formatFloat($item['P']));
            $station->setPriceNewDieselA($this->formatFloat($item['M']));
            $station->setPriceGasoline98($this->formatFloat($item['L']));
            $station->setPriceLiquefiedPetroleumGas($this->formatFloat($item['V']));
            $station->setLabel($item['Z']);
            $station->setSaleType($item['AA']);
            $station->setRem($item['AB']);
            $station->setSchedule($item['AC']);
            $station->setFile($file);
            $station->addPrice($price);
            $this->stationRepository->save($station);

            unset($price);
            unset($station);
            unset($item);
        }

        // Marco los antiguos como NO válidos
        $this->fileRepository->inactiveAllFiles();

        $file->setNumProcessed($num_processed);
        if ($num_processed >= $totalSheets) {
            // Marco el nuevo como válido
            $file->setActive(true);
        }

        $this->fileRepository->save($file);
    }

    private function isValidPrice(Price $price) {
        return (
            $price->getPriceGasoline95() > 0 ||
            $price->getPriceDieselA() > 0 ||
            $price->getPriceDieselB() > 0 ||
            $price->getPriceNewDieselA() > 0 ||
            $price->getPriceGasoline98() > 0 ||
            $price->getPriceLiquefiedPetroleumGas() > 0
        );
    }

    private function formatFloat($value) {
        return floatval(str_replace(',','.', $value));
    }
}
