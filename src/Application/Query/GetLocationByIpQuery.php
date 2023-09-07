<?php


namespace App\Application\Query;


use App\Shared\Domain\Bus\Query\Query;

class GetLocationByIpQuery extends Query
{
    private $ip;

    public function __construct(string $ip)
    {
        $this->ip = $ip;

        parent::__construct();
    }

    public function ip(): string
    {
        return $this->ip;
    }
}