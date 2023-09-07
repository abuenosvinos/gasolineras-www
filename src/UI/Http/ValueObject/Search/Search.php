<?php

declare(strict_types = 1);

namespace App\UI\Http\ValueObject\Search;

use Symfony\Component\HttpFoundation\Request;

class Search
{
    private $filter;
    private $order;
    private $page;

    public function __construct($filter, $order, $page)
    {
        $this->filter = $filter;
        $this->order = $order;
        $this->page = $page;
    }

    public function filter(): Filter
    {
        return $this->filter;
    }

    public function order(): Order
    {
        return $this->order;
    }

    public function page(): Page
    {
        return $this->page;
    }

    static public function fromRequest(Request $request)
    {
        return new self(
            Filter::fromRequest($request),
            Order::fromRequest($request),
            Page::fromRequest($request)
        );
    }
}
