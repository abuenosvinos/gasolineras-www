<?php

declare(strict_types = 1);

namespace App\UI\Http\ValueObject\Search;

use Symfony\Component\HttpFoundation\Request;

class Filter
{
    private $filters = [];

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function filters(): array
    {
        return $this->filters;
    }

    static public function fromRequest(Request $request)
    {
        $filters = [];
        foreach ($request->query->all() as $key => $value) {
            if (strpos($key, 'filter_') !== false) {
                $filters[str_replace('filter_', '', $key)] = $value;
            }
        }
        return new self($filters);
    }
}
