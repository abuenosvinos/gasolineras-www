<?php

declare(strict_types = 1);

namespace App\UI\Http\ValueObject\Search;

use Symfony\Component\HttpFoundation\Request;

class Order
{
    private $order = 'id';
    private $type = 'asc';

    public function __construct($order = 'id', $type = 'asc')
    {
        $this->order = $order;
        $this->type = $type;
    }

    public function order(): string
    {
        return $this->order;
    }

    public function type(): string
    {
        return $this->type;
    }

    static public function fromRequest(Request $request)
    {
        return new self(
            $request->query->get('order_by', 'id'),
            $request->query->get('order_type', 'asc')
        );
    }
}
