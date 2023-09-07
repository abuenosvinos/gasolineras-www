<?php


namespace App\Application\Query;


use App\Domain\Entity\File;
use App\Infrastructure\Doctrine\Repository\FileRepository;
use App\Infrastructure\Doctrine\Repository\StationRepository;
use App\Shared\Domain\Bus\Query\QueryHandler;
use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\Criteria\Filter;
use App\Shared\Domain\Criteria\FilterField;
use App\Shared\Domain\Criteria\FilterOperator;
use App\Shared\Domain\Criteria\Filters;
use App\Shared\Domain\Criteria\FilterValue;
use App\Shared\Domain\Criteria\Order;
use App\Shared\Domain\Criteria\OrderBy;
use App\Shared\Domain\Criteria\OrderType;

class SearchStationQueryHandler implements QueryHandler
{
    private $repository;
    private $fileRepository;

    public function __construct(StationRepository $repository, FileRepository $fileRepository)
    {
        $this->repository = $repository;
        $this->fileRepository = $fileRepository;
    }

    public function __invoke(SearchStationQuery $query)
    {
//        $file = $this->fileRepository->find(75);
//
//        $filter = new Filter(
//            new FilterField('file'),
//            new FilterOperator('='),
//            new FilterValue($file)
//        );
//        $filters = new Filters([$filter]);
        $filters = new Filters([]);
        $criteria = new Criteria(
            $filters, new Order(
                new OrderBy($query->search()->order()->order()),
                new OrderType($query->search()->order()->type())
            ),
            $query->search()->page()->offset(),
            $query->search()->page()->limit()
        );

        return $this->repository->searchByCriteria($criteria);
        //return $this->repository->getAll($query->page());
    }
}