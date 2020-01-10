<?php


namespace App\Application\Query;


use App\Infrastructure\Doctrine\Repository\StationRepository;
use App\Shared\Domain\Bus\Query\QueryHandler;

class FindAllStationQueryHandler implements QueryHandler
{
    private $repository;

    public function __construct(StationRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(FindAllStationQuery $query)
    {
        return $this->repository->getAll($query->page());
    }
}