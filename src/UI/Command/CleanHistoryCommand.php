<?php

namespace App\UI\Command;

use App\Shared\Domain\Bus\Command\CommandBus;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CleanHistoryCommand extends Command
{
    protected static $defaultName = 'app:clean-history';

    private $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Delete the oldest files')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $this->commandBus->dispatch(new \App\Application\Command\CleanHistoryCommand());

        $io->success('Execution succeed');
    }
}
