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
        $io = new SymfonyStyle($input, $output);

        $this->commandBus->dispatch(new GetDataCommand());

        $io->success('Execution succeed');
    }
}
