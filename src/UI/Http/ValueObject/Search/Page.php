<?php

declare(strict_types = 1);

namespace App\UI\Http\ValueObject\Search;

use Symfony\Component\HttpFoundation\Request;

class Page
{
    private $page = 1;
    private $limit = 10;

    public function __construct($page, $limit = 10)
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

    public function offset(): int
    {
        return $this->limit * ($this->page - 1);
    }

    public function first() :int
    {
        return 1 + ($this->limit * ($this->page - 1));
    }

    public function last() :int
    {
        return $this->limit + ($this->limit * ($this->page - 1));
    }

    static public function fromRequest(Request $request)
    {
        return new self($request->query->get('page', 1));
    }
}
