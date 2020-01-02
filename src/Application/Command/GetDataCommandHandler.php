<?php


namespace App\Application\Command;


use App\Application\Event\FileWasDownloaded;
use App\Infrastructure\Doctrine\Repository\FileRepository;
use App\Shared\Domain\Bus\Command\CommandHandler;
use App\Shared\Domain\Bus\Event\EventBus;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpClient\HttpClient;

class GetDataCommandHandler implements CommandHandler
{
    private $repository;
    private $params;
    private $eventBus;

    public function __construct(FileRepository $repository, ParameterBagInterface $params, EventBus $eventBus)
    {
        $this->repository = $repository;
        $this->params = $params;
        $this->eventBus = $eventBus;
    }

    public function __invoke(GetDataCommand $command)
    {
        $httpClient = HttpClient::create();
        $response = $httpClient->request('GET', $this->params->get('download_excel_url'), ['timeout' => 600]);

        $nameFile = 'file_'.date('Ymd_His').'.xls';
        $pathFile = $this->params->get('download_save_path') . '/' . $nameFile;
        file_put_contents($pathFile, $response->getContent());

        $spreadsheet = IOFactory::load($pathFile);
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        $name = $sheetData[1]['B'];
        unset($sheetData);
        unset($spreadsheet);
        unset($response);

        $file  = $this->repository->findOneBy(['name' => $name]);
        if (isset($file)) {
            //$io->error('Ya existe un fichero con ese nombre: ' . $name);
            return;
        }

        $file = $this->repository->createFile($name, $nameFile);

        $this->eventBus->notify(new FileWasDownloaded(['name' => $file->getName()]));
    }
}