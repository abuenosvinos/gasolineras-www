<?php


namespace App\Application\Command;


use App\Infrastructure\Doctrine\Repository\FileRepository;
use App\Shared\Domain\Bus\Command\CommandHandler;

class CleanHistoryCommandHandler implements CommandHandler
{
    private $repository;

    public function __construct(FileRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(CleanHistoryCommand $command)
    {
        /*
        $files = $this->entityManager->getRepository(File::class)->findBy([], ['id' => 'desc'], 3, 10);
        foreach ($files as $file) {
            $this->entityManager->remove($file);
        }
        */

        $files = $this->repository->getLastFiles();
        foreach ($files as $file) {
            $this->repository->remove($file);
        }
    }
}