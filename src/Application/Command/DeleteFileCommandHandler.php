<?php


namespace App\Application\Command;


use App\Infrastructure\Doctrine\Repository\FileRepository;
use App\Shared\Domain\Bus\Command\CommandHandler;

class DeleteFileCommandHandler implements CommandHandler
{
    private $repository;

    public function __construct(FileRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(DeleteFileCommand $command)
    {
        $file = $this->repository->find($command->id());
        if ($file) {
            $this->repository->remove($file);
        }
    }
}