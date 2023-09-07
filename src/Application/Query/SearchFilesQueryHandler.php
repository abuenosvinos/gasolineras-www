<?php


namespace App\Application\Query;


use App\Infrastructure\Doctrine\Repository\FileRepository;
use App\Shared\Domain\Bus\Query\QueryHandler;
use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\Criteria\Filters;
use App\Shared\Domain\Criteria\Order;
use App\Shared\Domain\Criteria\OrderBy;
use App\Shared\Domain\Criteria\OrderType;

class SearchFilesQueryHandler implements QueryHandler
{
    private $repository;

    public function __construct(FileRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(SearchFilesQuery $query)
    {
        $filters = new Filters([]);
        $criteria = new Criteria(
            $filters, new Order(
                new OrderBy($query->search()->order()->order()),
                new OrderType($query->search()->order()->type())
            ),
            $query->search()->page()->offset(),
            $query->search()->page()->limit()
        );

        return $this->repository->searchFilesWithTotalStations($criteria);
    }
}