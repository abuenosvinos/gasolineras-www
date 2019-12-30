<?php


namespace App\Application\Query;


use App\Shared\Domain\Bus\Query\Query;

class FindFileByIdQuery extends Query
{
    private $id;

    public function __construct(string $id)
    {
        $this->id = $id;

        parent::__construct();
    }

    public function id(): string
    {
        return $this->id;
    }
}