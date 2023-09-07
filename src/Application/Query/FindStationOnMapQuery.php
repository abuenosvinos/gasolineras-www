<?php


namespace App\Application\Query;


use App\Shared\Domain\Bus\Query\Query;

class FindStationOnMapQuery extends Query
{
    private $lat;
    private $lng;
    private $radius;
    private $gas;
    private $opened;

    public function __construct($lat, $lng, $radius, $gas, $opened)
    {
        $this->lat = $lat;
        $this->lng = $lng;
        $this->radius = $radius;
        $this->gas = $gas;
        $this->opened = $opened;

        parent::__construct();
    }

    public function lat()
    {
        return $this->lat;
    }

    public function lng()
    {
        return $this->lng;
    }

    public function radius()
    {
        return $this->radius;
    }

    public function gas()
    {
        return $this->gas;
    }

    public function opened()
    {
        return $this->opened;
    }
}
