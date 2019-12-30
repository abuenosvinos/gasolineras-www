<?php

namespace App\UI\Command;

use App\Application\Command\ProcessFileCommand;
use App\Shared\Domain\Bus\Command\CommandBus;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ProcessExcelCommand extends Command
{
    protected static $defaultName = 'app:process-excel';

    private $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;

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

        $this->commandBus->dispatch(new ProcessFileCommand($id));

        $io->success('Execution succeed. Process enqueued');
    }

}
