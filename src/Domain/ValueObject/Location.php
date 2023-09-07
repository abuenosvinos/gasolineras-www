<?php


namespace App\Domain\ValueObject;

class Location
{
    private $lat;
    private $lng;

    public function __construct($lat, $lng)
    {
        $this->lat = $lat;
        $this->lng = $lng;
    }

    public function lat()
    {
        return $this->lat;
    }

    public function lng()
    {
        return $this->lng;
    }
}
