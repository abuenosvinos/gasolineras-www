<?php

declare(strict_types = 1);

namespace App\Shared\Domain\ValueObject;

class Page
{
    private $page = 1;
    private $limit = 10;
    private $total = 0;

    public function __construct($page, $limit)
    {
        $this->page = (int)$page;
        $this->limit = (int)$limit;
    }

    public function page(): int
    {
        return $this->page;
    }

    public function limit(): int
    {
        return $this->limit;
    }

    public function numPages(): int
    {
        return ($this->total / $this->page);
    }

    public function first() :int
    {
        return 1 + ($this->limit * ($this->page - 1));
    }

    public function last() :int
    {
        return $this->limit + ($this->limit * ($this->page - 1));
    }
}
