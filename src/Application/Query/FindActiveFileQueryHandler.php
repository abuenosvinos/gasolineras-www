<?php


namespace App\Application\Query;


use App\Infrastructure\Doctrine\Repository\FileRepository;
use App\Shared\Domain\Bus\Query\QueryHandler;

class FindActiveFileQueryHandler implements QueryHandler
{
    private $repository;

    public function __construct(FileRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(FindActiveFileQuery $query)
    {
        return $this->repository->findActiveFile();
    }
}