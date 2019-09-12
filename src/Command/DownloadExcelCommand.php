<?php

namespace App\Command;

use App\Entity\File;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpClient\HttpClient;

class DownloadExcelCommand extends Command
{
    protected static $defaultName = 'app:download-excel';

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
            ->setDescription('Download Excel from Official Register')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /*
         * Otros sitios donde encontrar los datos o hacen referencia a ellos
         * https://sedeaplicaciones.minetur.gob.es/ServiciosRESTCarburantes/PreciosCarburantes/EstacionesTerrestres/
         * https://opendata.esri.es/datasets/gasolineras-de-espa%C3%B1a
         * https://datos.gob.es/es/catalogo/e04990201-precio-de-carburantes-en-las-gasolineras-espanolas
         */
        $io = new SymfonyStyle($input, $output);

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

        $file  = $this->entityManager->getRepository(File::class)->findOneBy(['name' => $name]);
        if (isset($file)) {
            $io->error('Ya existe un fichero con ese nombre: ' . $name);
            return;
        }

        $file = new File();
        $file->setName($name);
        $file->setFile($nameFile);
        $file->setActive(false);

        $this->entityManager->persist($file);
        $this->entityManager->flush();
        $this->entityManager->clear();

        $io->success('Execution succeed');
    }
}
