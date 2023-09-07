<?php


namespace App\Application\Query;


use App\Shared\Domain\Bus\Query\Query;
use App\UI\Http\ValueObject\Search\Search;

class SearchStationQuery extends Query
{
    private $search;

    public function __construct(Search $search)
    {
        $this->search = $search;

        parent::__construct();
    }

    public function search(): Search
    {
        return $this->search;
    }
}