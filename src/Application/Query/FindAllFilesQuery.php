<?php


namespace App\Application\Query;


use App\Shared\Domain\Bus\Query\Query;
use App\Shared\Domain\ValueObject\Page;

class FindAllFilesQuery extends Query
{
    private $page;

    public function __construct(Page $page)
    {
        $this->page = $page;

        parent::__construct();
    }

    public function page(): Page
    {
        return $this->page;
    }
}