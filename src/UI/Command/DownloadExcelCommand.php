<?php

namespace App\UI\Command;

use App\Application\Command\GetDataCommand;
use App\Shared\Domain\Bus\Command\CommandBus;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DownloadExcelCommand extends Command
{
    protected static $defaultName = 'app:download-excel';

    private $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;

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

        $this->commandBus->dispatch(new GetDataCommand());

        $io->success('Execution succeed');
    }
}
